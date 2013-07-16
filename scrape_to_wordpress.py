#!/usr/bin/python
# -*- coding: utf-8 -*-
info = """

scrape Mars From Space and post to Wordpress
page numbers to scrape as sys args
run like:

python scrape_to_wordpress.py 33 33

"""
import sys
from scraper_publisher_lib import *

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
scrape = Scrape(base_url=base_url, local_img_dir=local_img_dir,
                base_url_wallpapers=base_url_wallpapers)
publish = Publish(published_url=published_url,
                  previously_published=previously_published)

# grab links to all the detail pages we need
all_links = scrape.grab_all_page_urls(page_min, page_max)

# grab content each page and post to WP if not previously published
# limit
found = False
post_count = 0
for detail_url in all_links:

    if post_count > posts_limit:
        print "finished publishing " + str(posts_limit) + " posts, see you tomorrow."
        break;
    img_id = detail_url.split('/')[-1]
    if img_id not in previously_published:
        found = True
        print 'fetching data from ' + detail_url
        try:
            (title, content, detail_url, local_img_file) = \
                scrape.grab_content_from_page(detail_url)
        except:

                 # guh

            print 'nope'
            continue  # move along
        print 'posting to WP: ' + title

        # post to WP
        publish.post_to_wordpress(
            title,
            content,
            detail_url,
            local_img_file,
            previously_published,
            True,
            )
        post_count = post_count + 1

if not found:
    print 'all links were previously published'
print 'Bye!'
