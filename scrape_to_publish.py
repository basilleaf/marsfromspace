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
debug = True

import sys
import os
os.environ.setdefault("DJANGO_SETTINGS_MODULE", "api.settings")

import urllib2
from scrape import Scrape
from publish import WPPublish
from api.api_site.models import DetailPage

if not sys.argv[1]:
    sys.exit("please provide page start and end numbers \n " + info)

# currently we are counting down to 1..
page_min = int(sys.argv[1])
page_max = int(sys.argv[2])

posts_limit = 1  # only publish this many to WP at a time

base_url = 'http://hirise.lpl.arizona.edu/releases/all_captions.php'
# base_url_wallpapers = 'http://hirise.lpl.arizona.edu/'
base_url_wallpapers = 'http://static.uahirise.org/images/wallpaper/'
local_img_dir = '/app/tmp/'

# setup some tools
scrape = Scrape(base_url=base_url, local_img_dir=local_img_dir, base_url_wallpapers=base_url_wallpapers)
wp_publish = WPPublish()

if not debug:
    previously_published = wp_publish.get_all_published()
else:
    previously_published = []

# grab links to all the detail pages we need
all_detail_page_urls, urls_by_page = scrape.grab_all_page_urls(page_min, page_max)

# set to False if you don't wnat to publish to Wordpress
# this will also cause it to ignore previously published list
# grab content each page and publish to api and perhaps WP too
post_count = 0
last_page = 0
for detail_url in all_detail_page_urls:

    this_page = urls_by_page[detail_url]  # track what page of their wesite we are currently on
    if last_page != this_page:
        print "starting page " + str(this_page)
    last_page = this_page


    # we limit the amount of pages we post to wordpress at a time
    if post_count > posts_limit:
        print "finished publishing " + str(posts_limit) + " posts, see you tomorrow."
        break;

    img_id = detail_url.split('/')[-1]

    if img_id in previously_published:
        print "wp site says this is published already %s moving along" % img_id
        continue

    # this hasn't been published to WP yet OR we are only posting to api
    print 'fetching data from ' + detail_url
    this_scrape = scrape.grab_content_from_detail_page(detail_url)
    if not this_scrape:
        print 'scrape.grab_content_from_detail_page returned False'
        continue  # move along

    (title, content, detail_url) = this_scrape

    print 'posting to WP: ' + title
    print local_img_file
    # post to WP
    wp_posted = wp_publish.post_to_wordpress(title, content, detail_url, True)
    if wp_posted: 
        post_count = post_count + 1

        # post to api (if not already there)
        try: 
            obj, created = DetailPage.objects.get_or_create(title=title, content=content, detail_url=detail_url)
        except: 
            pass  # it was already in the database ?? todo: this in IntegrityError from Django but I'm not getting how to catch it here
    else: 
        print "could not post, wp_publish.post_to_wordpress returned False"

if post_count:
    print 'all links were previously published'
else: 
    print 'NOTHING PUBLISHED'

print 'Bye!'
