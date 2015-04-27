#!/usr/bin/python
# -*- coding: utf-8 -*-
import urllib2
import json
from time import sleep
from wordpress_xmlrpc import Client, WordPressPost
from wordpress_xmlrpc.methods.posts import NewPost
from wordpress_xmlrpc.methods import media
from wordpress_xmlrpc.compat import xmlrpc_client
from settings import WP_USER, WP_PW, BUCKET_NAME, AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY

class WPPublish:
    """
    for publishing to WP
    """

    def __init__(self, **kwargs):
        self.wp = Client('http://www.marsfromspace.com/xmlrpc.php', WP_USER, WP_PW)


    def post_to_wordpress(
        self,
        title,
        content,
        detail_url,
        image_upload_id,
        retry,
        ):
        
        # now post the post and the image
        post = WordPressPost()
        post.post_type = 'portfolio'
        post.title = title
        post.content = content
        post.post_status = 'publish'
        post.thumbnail = image_upload_id

        if self.wp.call(NewPost(post)):
            return True

    def get_all_published(self):
        print 'getting previously published, this is slow.. '
        wp_site_json = 'http://www.marsfromspace.com/?json=1&post_type=portfolio'
        response = urllib2.urlopen(wp_site_json)
        data = json.load(response)
        total_pages = data['pages']

        all_post_img_urls = []
        print "looking at %s pages: " % str(total_pages)
        for p in range(total_pages):
            page_no = str(p + 1)
            print 'getting previously published page ' + str(page_no)
            this_url = wp_site_json + "&page=" + str(page_no)
            print this_url
            response = urllib2.urlopen(this_url)
            data = json.load(response)
            for post in data['posts']:
                url = post['thumbnail_images']['full']['url']
                # print url
                all_post_img_urls.append(url)

        return all_post_img_urls

    def post_image(self, detail_url, scrape, retry):

        try:
            (local_file, img_url) = scrape.fetch_featured_image(detail_url)
        except TypeError:
            print "could not find a featured image, doing nothing"
            return False

        image_upload = {'name': local_file.split('/')[-1], 'type': 'image/jpg'}  # mimetype

        with open(local_file, 'rb') as img:
            image_upload['bits'] = xmlrpc_client.Binary(img.read())

            print 'uploading remote image ' + img_url + '  to wp'

            response = self.wp.call(media.UploadFile(image_upload))
            return (response['id'], img_url)

            """
            try:
                response = self.wp.call(media.UploadFile(image_upload))
                return (response['id'], url)
            except:

                 # occasionally response is 404, wait and try again
                if retry:
                    print 'sleep 3'
                    sleep(2)
                    # call self again
                    return self.post_image(detail_url, scrape, False)
                else:
                    print "couldn't connect to WP 2x to post image upload"
                    print local_file
                    print "post_to_wordpress returning false"
                    return False
        

            print "no suitable images found at " + url 
            print "post_to_wordpress returning false"
            return False
            """
                    


