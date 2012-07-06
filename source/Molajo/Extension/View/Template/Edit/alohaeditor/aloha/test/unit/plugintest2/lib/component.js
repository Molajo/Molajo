define(
    ['plugintest1/resourcetype'],
    function (resourcetype) {


        var resource = resourcetype.extend({
            doOther:function () {
                return 'didOther';
            }
        });

        return new resource();

    });
