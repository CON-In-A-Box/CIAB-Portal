var QuaggaApp = {
    fieldid: null,
    callback: null,
    init: function(field, cb) {
        var self = this;
        self.fieldid = field;
        self.callback = cb;

        self.showOverlay(this.stop);
        var drawingCtx = Quagga.canvas.ctx.overlay,
            drawingCanvas = Quagga.canvas.dom.overlay;
        if (drawingCtx && drawingCanvas)
            drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));

        Quagga.init(this.state, function(err) {
            if (err) {
                return self.handleError(err);
            }
            QuaggaApp.attachListeners();
            QuaggaApp.checkCapabilities();
            Quagga.start();
        });
    },
    stop: function() {
        Quagga.stop();
        QuaggaApp.hideOverlay();
    },
    handleError: function(err) {
        console.log(err);
    },
    checkCapabilities: function() {
        var track = Quagga.CameraAccess.getActiveTrack();
        var capabilities = {};
        if (typeof track.getCapabilities === 'function') {
            capabilities = track.getCapabilities();
        }
        this.applySettingsVisibility('zoom', capabilities.zoom);
        this.applySettingsVisibility('torch', capabilities.torch);
    },
    updateOptionsForMediaRange: function(node, range) {
        var NUM_STEPS = 6;
        var stepSize = (range.max - range.min) / NUM_STEPS;
        var option;
        var value;
        while (node.firstChild) {
            node.removeChild(node.firstChild);
        }
        for (var i = 0; i <= NUM_STEPS; i++) {
            value = range.min + (stepSize * i);
            option = document.createElement('option');
            option.value = value;
            option.innerHTML = value;
            node.appendChild(option);
        }
    },
    applySettingsVisibility: function(setting, capability) {
        // depending on type of capability
        if (typeof capability === 'boolean') {
            var node = document.querySelector('input[name="settings_' + setting + '"]');
            if (node) {
                node.parentNode.style.display = capability ? 'block' : 'none';
            }
            return;
        }
        if (window.MediaSettingsRange && capability instanceof window.MediaSettingsRange) {
            var node = document.querySelector('select[name="settings_' + setting + '"]');
            if (node) {
                this.updateOptionsForMediaRange(node, capability);
                node.parentNode.style.display = 'block';
            }
            return;
        }
    },
    initCameraSelection: function(){
        var streamLabel = Quagga.CameraAccess.getActiveStreamLabel();

        return Quagga.CameraAccess.enumerateVideoDevices();
    },
    attachListeners: function() {
        var self = this;
        self.initCameraSelection();
    },
    _accessByPath: function(obj, path, val) {
        var parts = path.split('.'),
            depth = parts.length,
            setter = (typeof val !== "undefined") ? true : false;

        return parts.reduce(function(o, key, i) {
            if (setter && (i + 1) === depth) {
                if (typeof o[key] === "object" && typeof val === "object") {
                    Object.assign(o[key], val);
                } else {
                    o[key] = val;
                }
            }
            return key in o ? o[key] : {};
        }, obj);
    },
    _convertNameToState: function(name) {
        return name.replace("_", ".").split("-").reduce(function(result, value) {
            return result + value.charAt(0).toUpperCase() + value.substring(1);
        });
    },
    applySetting: function(setting, value) {
        var track = Quagga.CameraAccess.getActiveTrack();
        if (track && typeof track.getCapabilities === 'function') {
            switch (setting) {
            case 'zoom':
                return track.applyConstraints({advanced: [{zoom: parseFloat(value)}]});
            case 'torch':
                return track.applyConstraints({advanced: [{torch: !!value}]});
            }
        }
    },
    setState: function(path, value) {
        var self = this;

        if (typeof self._accessByPath(self.inputMapper, path) === "function") {
            value = self._accessByPath(self.inputMapper, path)(value);
        }

        if (path.startsWith('settings.')) {
            var setting = path.substring(9);
            return self.applySetting(setting, value);
        }
        self._accessByPath(self.state, path, value);

        Quagga.stop();
        QuaggaApp.init(QuaggaApp.fieldid, QuaggaApp.callback);
    },
    inputMapper: {
        inputStream: {
            constraints: function(value){
                if (/^(\d+)x(\d+)$/.test(value)) {
                    var values = value.split('x');
                    return {
                        width: {min: parseInt(values[0])},
                        height: {min: parseInt(values[1])}
                    };
                }
                return {
                    deviceId: value
                };
            }
        },
        numOfWorkers: function(value) {
            return parseInt(value);
        },
        decoder: {
            readers: function(value) {
                if (value === 'ean_extended') {
                    return [{
                        format: "ean_reader",
                        config: {
                            supplements: [
                                'ean_5_reader', 'ean_2_reader'
                            ]
                        }
                    }];
                }
                return [{
                    format: value + "_reader",
                    config: {}
                }];
            }
        }
    },
    state: {
        inputStream: {
            type : "LiveStream",
            constraints: {
                width: {min: 640},
                height: {min: 480},
                facingMode: "environment",
                aspectRatio: {min: 1, max: 2}
            }
        },
        locator: {
            patchSize: "medium",
            halfSample: true
        },
        numOfWorkers: 2,
        frequency: 10,
        decoder: {
            readers : [{
                format: "ean_reader",
                config: {}
            }]
        },
        locate: true
    },
    showOverlay: function(cancelCb) {
        if (!this._overlay) {
            var content = document.createElement('div');
            var closeButton = document.createElement('div');

            closeButton.appendChild(document.createTextNode('X'));
            content.className = 'viewport overlay__content';
            content.id = 'interactive';
            closeButton.className = 'overlay__close';
            this._overlay = document.createElement('div');
            this._overlay.className = 'overlay';
            this._overlay.appendChild(content);
            content.appendChild(closeButton);
            closeButton.addEventListener('click', function closeClick() {
                closeButton.removeEventListener('click', closeClick);
                cancelCb();
            });
            document.body.appendChild(this._overlay);
        } else {
            var closeButton = document.querySelector('.overlay__close');
            closeButton.addEventListener('click', function closeClick() {
                closeButton.removeEventListener('click', closeClick);
                cancelCb();
            });
        }
        this._overlay.style.display = "block";
    },
    hideOverlay: function() {
        if (this._overlay) {
            this._overlay.style.display = "none";
        }
    },
};

Quagga.onProcessed(function(result) {
    var drawingCtx = Quagga.canvas.ctx.overlay,
        drawingCanvas = Quagga.canvas.dom.overlay;

    if (result) {
        if (result.boxes) {
            drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
            result.boxes.filter(function (box) {
                return box !== result.box;
            }).forEach(function (box) {
                Quagga.ImageDebug.drawPath(box, {x: 0, y: 1}, drawingCtx, {color: "green", lineWidth: 2});
            });
        }

        if (result.box) {
            Quagga.ImageDebug.drawPath(result.box, {x: 0, y: 1}, drawingCtx, {color: "#00F", lineWidth: 2});
        }

        if (result.codeResult && result.codeResult.code) {
            Quagga.ImageDebug.drawPath(result.line, {x: 'x', y: 'y'}, drawingCtx, {color: 'red', lineWidth: 3});
        }
    }
});

Quagga.onDetected(function(result) {
    /* Much of this is EAN based. if we use another format change the
       the processing here */
    var checkdigit = result.codeResult.code[1] +
                     result.codeResult.code[3] +
                     result.codeResult.code[5] +
                     result.codeResult.code[7] +
                     result.codeResult.code[9] +
                     result.codeResult.code[11];
    checkdigit *= 3;
    checkdigit += result.codeResult.code[2] +
                  result.codeResult.code[4] +
                  result.codeResult.code[6] +
                  result.codeResult.code[8] +
                  result.codeResult.code[10];

    checkdigit = 10 - (checkdigit % 10);

    if (checkdigit == result.codeResult.code[12])
    {
        var code = parseInt(result.codeResult.code.slice(0,-1));
        var code_field = document.getElementById(QuaggaApp.fieldid);
        code_field.value = code;
        QuaggaApp.callback(code);
        QuaggaApp.stop();
    }
});
