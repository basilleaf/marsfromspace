#!/usr/bin/python
# -*- coding: utf-8 -*-
import re
from time import sleep
import urllib
import urllib2
from bs4 import BeautifulSoup

class Scrape:
    """
    for scrapering
    """

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
        """

        grab all urls for detail pages as found on a range of index pages
        it counts backward from page_max to page_min

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
                all_cells = index_soup.findAll('td')  # each listing is in a table cell
                for cell in all_cells:
                    detail_url = '/'.join(self.base_url.split('/')[:-2]) + '/' + cell.a.get('href').split('/')[1:][0]
                    if detail_url == 'http://hirise.lpl.arizona.edu/releases':
                        continue  ## we don want this one ever
                    all_links.append(detail_url)
                    urls_by_page[detail_url] = i

            except:
                pass  # malformity

        return all_links, urls_by_page

    def grab_content_from_page(self, detail_url, fetch_local):
        """
        from detail page
        """

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
        img_url = self.base_url_wallpapers + img
        local_img_file = ''
        if fetch_local:
            local_img_file = \
                self.fetch_remote_file(img_url, True)
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

        return (title, content, detail_url, local_img_file, img_url)

    def prepare_content(self, content, detail_url):
        """
        some hacking of the text content found
        """

        # makes some inline reletive links into direct links
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


