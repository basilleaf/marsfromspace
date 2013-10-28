#!/usr/bin/python
# -*- coding: utf-8 -*-
import urllib2
from time import sleep
import boto
from wordpress_xmlrpc import Client, WordPressPost
from wordpress_xmlrpc.methods.posts import NewPost
from wordpress_xmlrpc.compat import xmlrpc_client
from settings import WP_USER, WP_PW, BUCKET_NAME, AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY

class WPPublish:
    """
    for publishing to WP
    """

    def __init__(self, **kwargs):
        self.published_url = kwargs['published_url']
        self.previously_published = kwargs['previously_published']

    def log_as_published(self, img_id):

        # grab previously published

        self.previously_published.append(img_id)

        # update the published log

        s3 = boto.connect_s3(AWS_ACCESS_KEY_ID, AWS_ACCESS_KEY_ID)
        bucket = s3.create_bucket(BUCKET_NAME)
        key_name = self.published_url.split('/').pop()
        bucket.delete_key(key_name)
        key = bucket.new_key(key_name)
        key.set_contents_from_string('\n'.join(self.previously_published))
        key.set_acl('public-read')

    def post_to_wordpress(
        self,
        title,
        content,
        detail_url,
        local_img_file,
        previously_published,
        retry,
        ):
        response = urllib2.urlopen(self.published_url)
        previously_published = [p.rstrip() for p in
                                response.readlines()]

        # first upload the image

        data = {'name': local_img_file.split('/')[-1],
                'type': 'image/jpg'}  # mimetype

        wp = Client('http://www.marsfromspace.com/xmlrpc.php', WP_USER,
                    WP_PW)

        # read the binary file and let the XMLRPC library encode it into base64

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
                return self.post_to_wordpress(
                    title,
                    content,
                    detail_url,
                    local_img_file,
                    previously_published,
                    False,
                    )
            else:
                print "couldn't connect to WP 2x,  moving along.."
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
            self.log_as_published(img_id)

    def remove_from_published(self, img_id):
        """
        mostly a utility func
        """

        # get the previously published list

        response = urllib2.urlopen(self.published_url)
        self.previously_published = [p.rstrip() for p in
                response.readlines()]

        # grab previously published

        if img_id not in self.previously_published:
            return 'img_id not found in previously_published'
        self.previously_published.remove(img_id)

        # update the published log
        s3 = boto.connect_s3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY)
        bucket = s3.create_bucket(BUCKET_NAME)
        key_name = self.published_url.split('/').pop()
        bucket.delete_key(key_name)
        key = bucket.new_key(key_name)
        key.set_contents_from_string('\n'.join(self.previously_published))
        key.set_acl('public-read')

