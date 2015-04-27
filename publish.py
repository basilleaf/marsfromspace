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

    def post_to_wordpress(
        self,
        title,
        content,
        detail_url,
        local_img_file,
        retry,
        ):
        
        img_id = detail_url.split('/')[-1]
        if img_id in self.previously_published():
            print "this id has been previously published " + img_id
            return False

        # first upload the image

        data = {'name': local_img_file.split('/')[-1], 'type': 'image/jpg'}  # mimetype

        wp = Client('http://www.marsfromspace.com/xmlrpc.php', WP_USER, WP_PW)

        # read the binary file and let the XMLRPC library encode it into base64
        print "read the binary file %s and let the XMLRPC library encode it into base64" % local_img_file
        with open(local_img_file, 'rb') as img:
            data['bits'] = xmlrpc_client.Binary(img.read())

        try:
            response = wp.call(media.UploadFile(data))
            attachment_id = response['id']
        except:

             # occasionally response is 404, wait and try again
            if retry:
                print 'sleep 3'
                sleep(3)
                # call self again
                return self.post_to_wordpress(
                    title,
                    content,
                    detail_url,
                    local_img_file,
                    False,
                    )
            else:
                print "couldn't connect to WP 2x to post image upload"
                print local_img_file
                print "post_to_wordpress returning false"
                return False

        # now post the post and the image
        post = WordPressPost()
        post.post_type = 'portfolio'
        post.title = title
        post.content = content
        post.post_status = 'publish'
        post.thumbnail = attachment_id

        if wp.call(NewPost(post)):
            img_id = local_img_file.split('/')[-1].split('.')[0]
            

        return True

    def get_all_published(self):
        print 'getting previously published, this is slow.. '
        wp_site_json = 'http://www.marsfromspace.com/?json=1&post_type=portfolio'
        response = urllib2.urlopen(wp_site_json)
        data = json.load(response)
        total_pages = data['pages']

        all_post_ids = []
        for p in range(total_pages):
            print 'getting previously published page ' + str(p)
            this_url = wp_site_json + "&page=" + str(p)
            response = urllib2.urlopen(this_url)
            data = json.load(response)
            for post in data['posts']:
                img_id = post['thumbnail_images']['full']['url'].split('/')[-1].split('.')[0]
                all_post_ids.append(img_id)

        return all_post_ids



