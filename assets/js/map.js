var qm = $;
qm(document).ready(function () {
    var map = new AMap.Map('container', {
            resizeEnable: true
        }),
        setting = {
            city: '010',
            radius: 1000
        };

    var app = {
        /**
         * 消息提示
         * @param message
         */
        message: function (message) {
            return qm('#messageModal').modal('show').find('.modal-body').html(message);
        },

        /**
         * 位置相关
         */
        gaoDe: {
            /**
             * 解析定位结果
             * @param data
             */
            onComplete: function (data) {
                var extend = {
                        /**
                         * 地理编码,返回地理编码结果
                         */
                        geoCoder: function (data) {
                            var geoCoder = new AMap.Geocoder(setting);
                            geoCoder.setCity(data.poi.adcode);
                            geoCoder.getLocation(data.poi.name, function (status, result) {
                                if (status === 'complete' && result.info === 'OK') {
                                    return extend.geoCoderCallBack(result);
                                }
                                return app.message('暂无数据！');
                            });
                        },

                        /**
                         * 地理编码返回结果展示
                         * @param data
                         */
                        geoCoderCallBack: function (data) {
                            var resultStr = ['选择成功'];
                            //地理编码结果数组
                            var geocode = data.geocodes;
                            for (var i = 0; i < geocode.length; i++) {
                                resultStr.push('地址:' + geocode[i].formattedAddress);
                                resultStr.push('地理编码结果是(坐标):');
                                resultStr.push('经度:' + geocode[i].location.getLng());
                                resultStr.push('纬度:' + geocode[i].location.getLat());
                                resultStr.push('匹配级别:' + geocode[i].level);
                                extend.addMarker(i, geocode[i]);
                            }
                            map.setFitView();
                            qm('#tips').removeClass('hide').addClass('alert-success').text(resultStr.join(' '));
                        },
                        addMarker: function (i, d) {
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
                        }
                    },
                    str = ['当前位置'];

                str.push('经度：' + data.position.getLng());
                str.push('纬度：' + data.position.getLat());

                //如为IP精确定位结果则没有精度信息
                if (data.accuracy) str.push('精度：' + data.accuracy + ' 米');

                str.push('是否经过偏移：' + (data.isConverted ? '是' : '否'));

                setting.city = data.addressComponent.citycode;
                var auto = new AMap.Autocomplete({
                    input: 'tipInput',
                    city: setting.city,
                    radius: setting.radius
                });

                AMap.event.addListener(auto, 'select', extend.geoCoder);//注册监听，当选中某条记录时会触发

                qm('#tips').removeClass('hide').addClass('alert-success').text(str.join(' '));
                qm('#tipInput').attr('placeholder', data.formattedAddress);

                return data;
            },

            /**
             * 解析定位错误信息
             * @param data
             */
            onError: function (data) {
                console.log(data);
                qm('#tips').removeClass('hide').addClass('alert-danger').text('定位失败');
                return app.message('定位失败');
            },

            /**
             * 定位
             * @returns {*}
             */
            position: function () {
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
                    AMap.event.addListener(geoLocation, 'complete', $this.onComplete);//返回定位信息
                    AMap.event.addListener(geoLocation, 'error', $this.onError);      //返回定位出错信息
                });
            }
        },

        /**
         * 运行
         */
        run: function () {
            var $this = this;
            $this.gaoDe.position();

            map.plugin(["AMap.ToolBar"], function () {
                map.addControl(new AMap.ToolBar());
            });

            if (location.href.indexOf('&guide=1') !== -1) {
                map.setStatus({scrollWheel: false})
            }
            
            qm(document).on('click', '.developMap', function () {
                qm('#container').toggleClass('hide');
            });
        }
    };

    app.run();
});