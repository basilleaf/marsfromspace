#!/usr/bin/python
# -*- coding: utf-8 -*-
info = """

This script both publishes to Wordpress and updates the local API database. 
These should probably be separate

scrape Mars From Space and post to Wordpress
page numbers to scrape as sys args
run like:

python scrape_to_publish.py 33 33

python scrape_to_publish.py 88 90

It also adds the published entry into the API database (for the tastypie api)

"""
import sys
import os
os.environ.setdefault("DJANGO_SETTINGS_MODULE", "api.settings")

from scrape import Scrape
from publish import WPPublish
from api.api_site.models import DetailPage

if not sys.argv[1]:
    sys.exit("please provide page start and end numbers \n " + info)

# currently we are counting down to 1..
page_min = int(sys.argv[1])
page_max = int(sys.argv[2])

posts_limit = 5  # only publish this many to WP at a time

base_url = 'http://hirise.lpl.arizona.edu/releases/all_captions.php'
base_url_wallpapers = 'http://hirise.lpl.arizona.edu/'
local_img_dir = '/tmp/'

published_url = 'https://s3.amazonaws.com/marsfromspace/published.txt'
response = urllib2.urlopen(published_url)
previously_published = [p.rstrip() for p in response.readlines()]

# setup some tools
scrape = Scrape(base_url=base_url, local_img_dir=local_img_dir, base_url_wallpapers=base_url_wallpapers)
wp_publish = WPPublish(published_url=published_url, previously_published=previously_published)

# grab links to all the detail pages we need
all_detail_page_urls, urls_by_page = scrape.grab_all_page_urls(page_min, page_max)

# set to False if you don't wnat to publish to Wordpress
# this will also cause it to ignore previously published list
wordpress_publish = False

# grab content each page and publish to api and perhaps WP too
found = False
post_count = 0
last_page = 0
for detail_url in all_detail_page_urls:

    this_page = urls_by_page[detail_url]  # track what page of their wesite we are currently on
    if last_page != this_page:
        print "starting page " + str(this_page)
    last_page = this_page


    if wordpress_publish:
        # we limit the amount of pages we post to wordpress at a time
        if post_count > posts_limit:
            print "finished publishing " + str(posts_limit) + " posts, see you tomorrow."
            break;

    img_id = detail_url.split('/')[-1]
    if img_id not in previously_published or wordpress_publish == False:
        # this hasn't been published to WP yet OR we are only posting to api
        found = True
        print 'fetching data from ' + detail_url
        try:
            (title, content, detail_url, local_img_file, img_url) = \
                scrape.grab_content_from_detail_page(detail_url, wordpress_publish)
        except:
            print 'nope'
            continue  # move along

        if wordpress_publish == True:
            print 'posting to WP: ' + title
            # post to WP
            wp_publish.post_to_wordpress(title, content, detail_url, local_img_file, previously_published, True)
            post_count = post_count + 1

        # post to api (if not already there)
        try: 
            obj, created = DetailPage.objects.get_or_create(title=title, content=content, detail_url=detail_url, img_url=img_url)
        except django.db.utils.IntegrityError; 
            pass  # it was already in the database


if not found:
    print 'all links were previously published'
print 'Bye!'
