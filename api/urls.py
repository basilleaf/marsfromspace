from django.conf.urls import patterns, include, url
from api_site.models import DetailPage
from api_site.api import EntryResource
# Uncomment the next two lines to enable the admin:
# from django.contrib import admin
# admin.autodiscover()


entry_resource = EntryResource()

urlpatterns = patterns('',
    # The normal jazz here...
    (r'^api/', include(entry_resource.urls)),
    # Examples:
    # url(r'^$', 'api.views.home', name='home'),
    # url(r'^api/', include('api.foo.urls')),

    # Uncomment the admin/doc line below to enable admin documentation:
    # url(r'^admin/doc/', include('django.contrib.admindocs.urls')),

    # Uncomment the next line to enable the admin:
    # url(r'^admin/', include(admin.site.urls)),
)
