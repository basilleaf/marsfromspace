#!/usr/bin/python
# -*- coding: utf-8 -*-
from scraper_publisher_lib import *

# currently we are counting down to 1..
page_min = 34
page_max = 34

posts_limit = 5  # only publish this many to WP at a time

base_url = 'http://hirise.lpl.arizona.edu/releases/all_captions.php'
base_url_wallpapers = 'http://hirise.lpl.arizona.edu/'
local_img_dir = '/Users/lballard/projects/marsfromspace/images/'
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

# creds on heroku go like..
WP_USER = os.environ['WP_USER']
WP_PW = os.environ['WP_PW']
AWS_ACCESS_KEY_ID = os.environ['AWS_ACCESS_KEY_ID']
AWS_SECRET_ACCESS_KEY = os.environ['AWS_SECRET_ACCESS_KEY']
BUCKET_NAME = os.environ['BUCKET_NAME']


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
