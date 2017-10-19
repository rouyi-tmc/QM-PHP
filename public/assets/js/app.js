layui.define(['layer', 'flow', 'form', 'laytpl'], function (exports) {
    var qm = layui.jquery,
        LayTpl = layui.laytpl;

    var app = {

        extend: {
            /**
             * 解析模板
             * @param dom
             * @param data
             * @returns {string|void|XML}
             */
            template: function (dom, data) {
                var template = qm('#' + dom).html();
                var reg = new RegExp("\\[([^\\[\\]]*?)\\]", 'igm');
                return template.replace(reg, function (node, key) {
                    return data[key];
                });
            },

            /**
             * 消息提示
             * @param message
             */
            message: function (message) {
                return qm('#messageModal').modal('show').find('.modal-body').html(message);
            },

            /**
             * AJAX 请求
             * @param url
             * @param data
             * @param fn
             * @param type
             * @returns {boolean}
             */
            request: function (url, data, fn, type) {
                var $this = this,
                    canBack = false;
                type = (type) ? type : 'POST';
                url = '/' + url;
                qm.ajax({
                    url: url,
                    data: data,
                    dataType: 'JSON',
                    type: type,
                    async: false,
                    success: function (rs) {
                        canBack = rs;
                    },
                    error: function () {
                        return $this.message('请求出错！');
                    }
                });

                return (fn) ? fn(canBack) : canBack;
            },

            page: function (url, data) {
                layui.flow.load({
                    elem: '#itemsBox',
                    end: '已全部加载完毕',
                    done: function (page, next) {
                        qm(this.elem).removeClass('hide');
                        var lis = [],
                            getTpl = qm('#itemsTemplate').html();
                        setTimeout(function () {
                            app.extend.request(url, {
                                page: page,
                                latitude: data.latitude,
                                longitude: data.longitude,
                                category: data.category || ''
                            }, function (rs) {
                                if (!rs) return false;
                                if (rs.code <= 0 && rs.message) return app.extend.message(rs.message);
                                LayTpl(getTpl).render(rs.data, function (html) {
                                    lis.push(html);
                                });
                                return next(lis.join(''), page < rs.data.last_page);
                            }, 'GET');
                        }, 1000);
                    }
                });
            }
        },

        /**
         * 位置相关
         */
        gaoDe: {
            extend: function () {
                var $this = this;
                return {
                    /**
                     * 地理编码返回结果展示
                     * @param map
                     * @param data
                     */
                    geoCoderCallBack: function (map, data) {
                        var resultStr = ['选择成功'];
                        //地理编码结果数组
                        var geocode = data.geocodes;
                        for (var i = 0; i < geocode.length; i++) {
                            resultStr.push('地址:' + geocode[i].formattedAddress);
                            resultStr.push('地理编码结果是(坐标):');
                            resultStr.push('经度:' + geocode[i].location.getLng());
                            resultStr.push('纬度:' + geocode[i].location.getLat());
                            resultStr.push('匹配级别:' + geocode[i].level);
                            $this.addMarker(map, i, geocode[i]);
                        }
                        map.setFitView();
                        qm('#tips').removeClass('hide').addClass('alert-success').text(resultStr.join(' '));
                    },

                    /**
                     * 添加标记
                     * @param map
                     * @param i
                     * @param d
                     */
                    addMarker: function (map, i, d) {
                        var marker = new AMap.Marker({
                            map: map,
                            position: [d.location.getLng(), d.location.getLat()]
                        });
                        var infoWindow = new AMap.InfoWindow({
                            content: d.formattedAddress,
                            offset: {x: 0, y: -30}
                        });
                        marker.on('mouseover', function (e) {
                            infoWindow.open(map, marker.getPosition());
                        });
                    },

                    /**
                     * 位置搜索
                     * @param map
                     */
                    search: function (map) {
                        AMap.plugin(['AMap.Autocomplete', 'AMap.PlaceSearch'], function () {
                            var autoOptions = {
                                city: '北京', //城市，默认全国
                                input: 'tipInput'//使用联想输入的input的id
                            };
                            var autocomplete = new AMap.Autocomplete(autoOptions);
                            var placeSearch = new AMap.PlaceSearch({
                                city: '北京',
                                map: map
                            });
                            AMap.event.addListener(autocomplete, "select", function (e) {
                                var data = e.poi || {};
                                placeSearch.setCity(data.adcode);
                                placeSearch.search(data.name);
                                var category = qm('.layui-select-title .layui-input').val(),
                                    postData = {
                                        latitude: data.location.O,
                                        longitude: data.location.M,
                                        category: category
                                    };
                                console.log(postData);
                                qm('#location').val(JSON.stringify({
                                    latitude: postData.latitude,
                                    longitude: postData.longitude
                                }));
                                app.extend.page('gather/getLocation', postData);
                            });
                        });
                    }
                }
            },

            /**
             * 定位
             * @param map
             * @param setting
             * @returns {*}
             */
            position: function (map, setting) {
                var $this = this;
                return map.plugin('AMap.Geolocation', function () {
                    var geoLocation = new AMap.Geolocation({
                        enableHighAccuracy: true,//是否使用高精度定位，默认:true
                        timeout: 10000,          //超过10秒后停止定位，默认：无穷大
                        buttonOffset: new AMap.Pixel(10, 20),//定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
                        zoomToAccuracy: true,      //定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false
                        buttonPosition: 'RB'
                    });

                    map.addControl(geoLocation);
                    geoLocation.getCurrentPosition();

                    AMap.event.addListener(geoLocation, 'complete', function (data) {
                        // 返回定位信息
                        var str = ['当前位置'];
                        str.push('经度：' + data.position.getLng());
                        str.push('纬度：' + data.position.getLat());

                        //如为IP精确定位结果则没有精度信息
                        if (data.accuracy) str.push('精度：' + data.accuracy + ' 米');

                        str.push('是否经过偏移：' + (data.isConverted ? '是' : '否'));

                        qm('#tips').removeClass('hide').addClass('alert-success').text(str.join(' '));
                        qm('#tipInput').attr('placeholder', data.formattedAddress);

                        setting.city = data.addressComponent.citycode;
                        var auto = new AMap.Autocomplete({
                            input: 'tipInput',
                            city: setting.city,
                            radius: setting.radius
                        });

                        // 注册监听，当选中某条记录时会触发
                        return AMap.event.addListener(auto, 'select', function () {
                            var geoCoder = new AMap.Geocoder(setting);
                            geoCoder.setCity(data.poi.adcode);
                            geoCoder.getLocation(data.poi.name, function (status, result) {
                                if (status === 'complete' && result.info === 'OK') {
                                    return $this.geoCoderCallBack(map, result);
                                }
                                return app.extend.message('暂无数据！');
                            });
                        });
                    });

                    AMap.event.addListener(geoLocation, 'error', function () {
                        // 返回定位出错信息
                        qm('#tipInput').attr('placeholder', '请输入地址进行查询');
                        console.log('当前位置获取失败！');
                        return app.gaoDe.extend().search(map);
                    });
                });
            }
        },

        /**
         * 地图
         */
        map: function () {
            var $this = this,
                map = new AMap.Map('container', {
                    resizeEnable: true,
                    center: [116.397428, 39.90923],//地图中心点
                    zoom: 13,//地图显示的缩放级别
                    keyboardEnable: false
                }),
                setting = {
                    city: '010',
                    radius: 1000
                };
            $this.gaoDe.position(map, setting);

            map.plugin(["AMap.ToolBar"], function () {
                map.addControl(new AMap.ToolBar());
            });

            if (location.href.indexOf('&guide=1') !== -1) {
                map.setStatus({scrollWheel: false})
            }

            if (typeof map !== 'undefined') {
                map.on('complete', function () {
                    if (location.href.indexOf('guide=1') !== -1) {
                        map.setStatus({
                            scrollWheel: false
                        });
                        if (location.href.indexOf('litebar=0') === -1) {
                            map.plugin(["AMap.ToolBar"], function () {
                                var options = {
                                    liteStyle: true
                                };
                                if (location.href.indexOf('litebar=1') !== -1) {
                                    options.position = 'LT';
                                    options.offset = new AMap.Pixel(10, 40);
                                } else if (location.href.indexOf('litebar=2') !== -1) {
                                    options.position = 'RT';
                                    options.offset = new AMap.Pixel(20, 40);
                                } else if (location.href.indexOf('litebar=3') !== -1) {
                                    options.position = 'LB';
                                } else if (location.href.indexOf('litebar=4') !== -1) {
                                    options.position = 'RB';
                                }
                                map.addControl(new AMap.ToolBar(options));
                            });
                        }
                    }
                });
            }

            qm(document).on('click', '.developMap', function () {
                if (qm(this).hasClass('on'))
                    qm(this).removeClass('on').text('收起地图');
                else
                    qm(this).addClass('on').text('展开地图');
                qm('#container').toggleClass('hide');
            });
        },

        /**
         * 运行
         */
        run: function () {
            var $this = this;
            qm('.layui-anim-upbit dd').unbind('click').click(function () {
                var value = qm(this).attr('lay-value');
                qm(this).parents('.layui-form-select').find('.layui-input').val(value);
                var location = qm('#location').val();
                if (value && location) {
                    location = JSON.parse(location);
                    qm('#itemsBox').html('');
                    app.extend.page('gather/getLocation', {
                        latitude: location.latitude,
                        longitude: location.longitude,
                        category: value
                    });
                }
            });
        }
    };
    exports('app', app);
});

layui.use('app', function (app) {
    app.run();
});