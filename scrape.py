#!/usr/bin/python
# -*- coding: utf-8 -*-
import re
from time import sleep
import urllib
import urllib2
from bs4 import BeautifulSoup
from wordpress_xmlrpc.methods import media

class Scrape:
    """
    for scrapering
    """

    def __init__(self, **kwargs):
        self.base_url = kwargs['base_url']
        self.local_img_dir = kwargs['local_img_dir']
        self.base_url_wallpapers = kwargs['base_url_wallpapers']

    def grab_all_page_urls(self, page_min, page_max):
        """

        grab all urls for detail pages as found on a range of index pages
        it counts backward from attributes page_max to page_min

        """
        page_urls = []
        print self.base_url

        # construct the index page urls from self.base_url and page no

        all_links = []
        urls_by_page = {}
        for i in range(page_max + 1, page_min, -1):

            url = self.base_url + '?page=%s' % str(i)
            page_urls.append(self.base_url + '?page=%s' % str(i))

            try:
                index_page = urllib2.urlopen(url).read()
            except urllib2.HTTPError:
                print 'urlopen fail ' + url
                continue

            try:
                index_soup = BeautifulSoup(index_page)
                all_cells = index_soup.findAll('td')[::-1]  # each listing is in a table cell
                for cell in all_cells:  
                    detail_url = '/'.join(self.base_url.split('/')[:-2]) + '/' + cell.a.get('href').split('/')[1:][0]
                    if detail_url == 'http://hirise.lpl.arizona.edu/releases':
                        continue  ## we don want this one ever
                    all_links.append(detail_url)
                    urls_by_page[detail_url] = i

            except:
                pass  # malformity

        return all_links, urls_by_page

    def grab_content_from_detail_page(self, detail_url):
        """
        returns content from detail page 
        """

        try:
            index_page = urllib2.urlopen(detail_url).read()
        except urllib2.HTTPError:
            print 'FAIL could not urlopen ' + detail_url
            return False

        soup = BeautifulSoup(index_page)

        try:
            title = soup.findAll('a', {'id': 'example1'})[0].get('title')
        except IndexError:
            print 'could not find title'
            return False  # no title no post move along

        # scrape the content and clean it up a bit..

        content = re.findall(r'<div class="caption-text">\s*(.*?)\s*<div class="social">', ' '.join(str(soup).splitlines()))[0]
        soup_content = BeautifulSoup(content)
        content = soup_content.prettify()
        content = self.prepare_content(content, detail_url)

        linkback = '<div class = "linkback" data-linkback="%s"></div>' % detail_url
        content = linkback + content

        return (title, content, detail_url)


    def prepare_content(self, content, detail_url):
        """
        this is a place to put hacking of the text content
        like replacing their internal links to display as external links on our site
        """

        if not content: 
            print 'empty content passed to scrape.prepare_content'

        # makes some inline reletive links into direct links
        content = content.replace('\n', ' ')
        content = content.replace('href="images/', 'target = "_blank" href="http://hirise.lpl.arizona.edu/images/')
        content = content.replace('href="E', 'target = "_blank" href="http://hirise.lpl.arizona.edu/E')
        content = content.replace('href="T', 'target = "_blank" href="http://hirise.lpl.arizona.edu/T')
        content = content.replace('href="r', 'target = "_blank" href="http://hirise.lpl.arizona.edu/r')
        content = content.replace('href="j', 'target = "_blank" href="http://hirise.lpl.arizona.edu/j')
        content = content.replace('href="p', 'target = "_blank" href="http://hirise.lpl.arizona.edu/p')
        content = content.replace('href="d', 'target = "_blank" href="http://hirise.lpl.arizona.edu/d')
        content = content.replace('href="e', 'target = "_blank" href="http://hirise.lpl.arizona.edu/e')
        content = content.replace('href="P', 'target = "_blank" href="http://hirise.lpl.arizona.edu/P')

        # add credit
        html_more_info = '<p>More info and image formats at <a target = "_blank" href = "%s">%s</a></p>'
        content += html_more_info % (detail_url, detail_url)
        content += '<p>Image: NASA/JPL/University of Arizona </p>'

        if not content: 
            print 'prepare_content deleted the content?'

        return content

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

    def fetch_featured_image(self, detail_url):
        img_id = detail_url.split('/')[-1]

        # add the base wallpapers url you can view all the sizes they have available..
        # we are doing to go looking for sizes, this lists the sizes we want in order of 
        # our preference
        size_list = ['1280', '1440','1600','1920','2048','2560','2880','1152','1024','800']

        if not img_id:
            print "could not find img_id from " + detail_url
            return False

        for sz in size_list: 
            url = '%s%s/%s.jpg' % (self.base_url_wallpapers, sz, img_id)
            local_file = self.fetch_remote_file(url, True)

            if local_file:
                return local_file, url


