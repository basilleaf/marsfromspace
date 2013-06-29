#!/usr/bin/python
# -*- coding: utf-8 -*-
from scraper_publisher_lib import *

# currently we are counting down to 1..
page_min = 34
page_max = 34

base_url = 'http://hirise.lpl.arizona.edu/releases/all_captions.php'
base_url_wallpapers = 'http://hirise.lpl.arizona.edu/'
local_img_dir = '/Users/lballard/projects/marsfromspace/images/'
published_url = 'https://s3.amazonaws.com/marsfromspace/published.txt'

response = urllib2.urlopen(published_url)
previously_published = [p.rstrip() for p in response.readlines()]

scrape = Scrape(base_url=base_url, local_img_dir=local_img_dir,
                base_url_wallpapers=base_url_wallpapers)
publish = Publish(published_url=published_url,
                  previously_published=previously_published)

all_links = scrape.grab_all_page_urls(page_min, page_max)
found = False

for detail_url in all_links:
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

        publish.post_to_wordpress(
            title,
            content,
            detail_url,
            local_img_file,
            previously_published,
            True,
            )

if not found:
    print 'all links were previously published'
print 'Bye!'
