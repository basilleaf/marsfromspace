# myapp/api.py
from tastypie.resources import ModelResource
from api.models import DetailPage


class EntryResource(ModelResource):
    class Meta:
        queryset = DetailPage.objects.all()
        resource_name = 'entry'