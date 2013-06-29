#!/usr/bin/python
# -*- coding: utf-8 -*-
import re
from random import randint
from time import sleep
import urllib
import urllib2
import boto
from bs4 import BeautifulSoup
import xmlrpclib
from wordpress_xmlrpc import Client, WordPressPost
from wordpress_xmlrpc.methods.posts import NewPost
from wordpress_xmlrpc.compat import xmlrpc_client
from wordpress_xmlrpc.methods import media, posts
from secrets import *


class Scrape:

    def __init__(self, **kwargs):
        self.base_url = kwargs['base_url']
        self.local_img_dir = kwargs['local_img_dir']
        self.base_url_wallpapers = kwargs['base_url_wallpapers']

    def fetch_remote_file(self, url, repeat):
        local_file = self.local_img_dir + url.split('/')[-1]
        try:
            print 'making ' + str(local_file)
            urllib.urlretrieve(url, local_file)
            with open(local_file):
                pass
            return local_file
        except IOError:
            if repeat:  # try again..
                sleep(3)
                return self.fetch_remote_file(url, False)
            else:
                print "can't fetch remote file " + url
                return False

    def grab_all_page_urls(self, page_min, page_max):

        page_urls = []
        print self.base_url

        # construct the index page urls from self.base_url and page no

        for i in range(page_max + 1, page_min, -1):
            page_urls.append(self.base_url + '?page=%s' % str(i))

        all_links = []
        for url in page_urls:
            try:
                index_page = urllib2.urlopen(url).read()
            except urllib2.HTTPError:
                print 'urlopen fail ' + url
                continue

            try:
                index_soup = BeautifulSoup(index_page)
                all_cells = index_soup.findAll('td')  # each listing is in a table cell
                for cell in all_cells:
                    all_links.append('/'.join(self.base_url.split('/'
                            )[:-2]) + '/' + cell.a.get('href').split('/'
                            )[1:][0])
            except:
                pass  # malformity

        return all_links

    def grab_content_from_page(self, detail_url):

        try:
            index_page = urllib2.urlopen(detail_url).read()
        except urllib2.HTTPError:
            print 'FAIL ' + detail_url
            return False

        soup = BeautifulSoup(index_page)

        img = ''
        for l in soup.findAll('a'):
            try:
                if str(l.contents[0]) == '1280':  # designer lady wants the 1280 image, sometimes there isn't one..
                    img = str(l.get('href'))
            except (IndexError, UnicodeEncodeError), e:
                pass  # none or strange link contents no worries

        if not img:
            print 'no suitable image found'
            return False  # if we can't get the 1280 image we are passing on this page ..

        # fetch the image so we have it locally

        local_img_file = \
            self.fetch_remote_file(self.base_url_wallpapers + img, True)
        if not local_img_file:
            print "couldn't fetch remot file it, move along"
            return False

        try:
            title = soup.findAll('a', {'id': 'example1'})[0].get('title'
                    )
        except IndexError:
            print 'could find title'
            return False  # no title no post move along

        # scrape the content and clean it up a bit..

        content = \
            re.findall(r'<div class="caption-text">\s*(.*?)\s*<div class="social">'
                       , ' '.join(str(soup).splitlines()))[0]
        soup_content = BeautifulSoup(content)
        content = soup_content.prettify()
        content = self.prepare_content(content, detail_url)

        return (title, content, detail_url, local_img_file)

    def prepare_content(self, content, detail_url):

        # makes inline reletive links into direct links

        content = content.replace('\n', ' ').replace('href="images/',
                'target = "_blank" href="http://hirise.lpl.arizona.edu/images/'
                ).replace('href="E',
                          'target = "_blank" href="http://hirise.lpl.arizona.edu/E'
                          ).replace('href="T',
                                    'target = "_blank" href="http://hirise.lpl.arizona.edu/T'
                                    ).replace('href="r',
                'target = "_blank" href="http://hirise.lpl.arizona.edu/r'
                ).replace('href="j',
                          'target = "_blank" href="http://hirise.lpl.arizona.edu/j'
                          ).replace('href="p',
                                    'target = "_blank" href="http://hirise.lpl.arizona.edu/p'
                                    ).replace('href="d',
                'target = "_blank" href="http://hirise.lpl.arizona.edu/d'
                ).replace('href="e',
                          'target = "_blank" href="http://hirise.lpl.arizona.edu/e'
                          ).replace('href="P',
                                    'target = "_blank" href="http://hirise.lpl.arizona.edu/P'
                                    )

        # add credit

        content += \
            '<p>More info and image formats at <a target = "_blank" href = "%s">%s</a></p>' \
            % (detail_url, detail_url)
        content += '<p>Image: NASA/JPL/University of Arizona </p>'

        return content


class Publish:

    def __init__(self, **kwargs):
        self.published_url = kwargs['published_url']
        self.previously_published = kwargs['previously_published']

    def log_as_published(self, img_id):

        # grab previously published

        self.previously_published.append(img_id)

        # update the published log

        s3 = boto.connect_s3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY)
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
