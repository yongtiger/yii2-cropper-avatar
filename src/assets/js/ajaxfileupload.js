/**
 * js-ajaxfileupload
 *
 * @link        http://www.brainbook.cc
 * @see         https://github.com/yongtiger/js-ajaxfileupload
 * @author      Tiger Yong <tigeryang.brainbook@outlook.com>
 * @copyright   Copyright (c) 2016 BrainBook.CC
 * @license     http://opensource.org/licenses/MIT
 *
 * Reference: https://github.com/carlcarl/AjaxFileUpload
 *
 * Features: 
 * - add `handleError`
 * - add `start` and `beforeSend` local callbacks of events
 * - fix ie9 and ie 10
 * - fix bugs: confict with yii.js(350)
 *
 * Example:
 *
 * ```js
 * ajaxFileUpload: function () {
 *     $.ajaxFileUpload({url: '<your url>', 
 *         secureuri: false,
 *         fileElementId:'<your file element id',
 *         type: 'post',
 *         data: '<your data>',
 *         dataType: 'json',
 * 
 *         beforeSend: function () {
 *             // ...
 *         },
 * 
 *         success: function (data, status) {
 *             // ...
 *         },
 * 
 *         error: function (XMLHttpRequest, textStatus, errorThrown) {
 *             // ...
 *         },
 * 
 *         complete: function () {
 *             // ...
 *         }
 *     });
 * },
 * ```
 *
 */

jQuery.extend({

    ///@see http://www.cnblogs.com/zrp2013/archive/2013/05/29/3106435.html
    handleError: function (s, xhr, status, e) {
         if (s.error) {
             s.error.call(s.context || s, xhr, status, e);
         }
         if (s.global) {
             (s.context ? jQuery(s.context) : jQuery.event).trigger("ajaxError", [xhr, s, e]);
         }
     },
     
     httpData: function (xhr, type, s) {
         var ct = xhr.getResponseHeader("content-type"),
         xml = type == "xml" || !type && ct && ct.indexOf("xml") >= 0,
         data = xml ? xhr.responseXML : xhr.responseText;
         if (xml && data.documentElement.tagName == "parsererror")
             throw "parsererror";
         if (s && s.dataFilter)
             data = s.dataFilter(data, type);
         if (typeof data === "string") {
             if (type == "script")
                 jQuery.globalEval(data);
             if (type == "json")
                 data = window["eval"]("(" + data + ")");
         }
         return data;
     },
     ///[http://www.brainbook.cc]

    createUploadIframe: function(id, uri)
    {
        //create frame
        var frameId = 'jUploadFrame' + id;

        if(window.ActiveXObject) {
            ///fix ie9 and ie 10 @see http://www.oschina.net/question/1246890_142999
            if(jQuery.browser.version=="9.0" || jQuery.browser.version=="10.0"){  
                var io = document.createElement('iframe');  
                io.id = frameId;  
                io.name = frameId;  
            }else if(jQuery.browser.version=="6.0" || jQuery.browser.version=="7.0" || jQuery.browser.version=="8.0"){  
                 var io = document.createElement('<iframe id="' + frameId + '" name="' + frameId + '" />');  
                 if(typeof uri== 'boolean'){  
                     io.src = 'javascript:false';  
                 }  
                 else if(typeof uri== 'string'){  
                     io.src = uri;  
                 }  
            }  
        }  
        else {
            var io = document.createElement('iframe');
            io.id = frameId;
            io.name = frameId;
        }
        io.style.position = 'absolute';
        io.style.top = '-1000px';
        io.style.left = '-1000px';

        document.body.appendChild(io);

        return io
    },
    createUploadForm: function(id, fileElementId)
    {
        //create form
        var formId = 'jUploadForm' + id;
        var fileId = 'jUploadFile' + id;
        var form = $('<form  action="" method="POST" name="' + formId + '" id="' + formId + '" enctype="multipart/form-data"></form>');
        var oldElement = $('#' + fileElementId);
        var newElement = $(oldElement).clone();
        $(oldElement).attr('id', fileId);
        $(oldElement).before(newElement);
        $(oldElement).appendTo(form);
        //set attributes
        $(form).css('position', 'absolute');
        $(form).css('top', '-1200px');
        $(form).css('left', '-1200px');
        $(form).appendTo('body');
        return form;
    },
    addOtherRequestsToForm: function(form,data)
    {
        // add extra parameter
        var originalElement = $('<input type="hidden" name="" value="">');
        for (var key in data) {
            name = key;
            value = data[key];
            var cloneElement = originalElement.clone();
            cloneElement.attr({'name':name,'value':value});
            $(cloneElement).appendTo(form);
        }
        return form;
    },

    ajaxFileUpload: function(s) {
        // TODO introduce global settings, allowing the client to modify them for all requests, not only timeout
        s = jQuery.extend({}, jQuery.ajaxSettings, s);
        var id = new Date().getTime()
        var form = jQuery.createUploadForm(id, s.fileElementId);
        if ( s.data ) form = jQuery.addOtherRequestsToForm(form,s.data);
        var io = jQuery.createUploadIframe(id, s.secureuri);
        var frameId = 'jUploadFrame' + id;
        var formId = 'jUploadForm' + id;

        // Watch for a new set of requests
        if ( !jQuery.active++ ) {

            ///[fix:beforeSend]@see http://www.developwebapp.com/5538869/
            ///If a local callback was specified, fire it and pass it the data
            if ( s.start )
                s.start( s );

            if (s.global) {
                jQuery.event.trigger( "ajaxStart" );
            }
        }

        var requestDone = false;

        // Create the request object
        var xml = {};

        ///[fix:beforeSend]@see http://www.developwebapp.com/5538869/
        ///If a local callback was specified, fire it and pass it the data
        if ( s.beforeSend )
            s.beforeSend( s );

        if ( s.global )
            jQuery.event.trigger("ajaxSend", [xml, s]);

        // Wait for a response to come back
        var uploadCallback = function(isTimeout)
        {
            var io = document.getElementById(frameId);
            try
            {
                if(io.contentWindow)
                {
                    xml.responseText = io.contentWindow.document.body?io.contentWindow.document.body.innerHTML:null;
                    xml.responseXML = io.contentWindow.document.XMLDocument?io.contentWindow.document.XMLDocument:io.contentWindow.document;

                }else if(io.contentDocument)
                {
                    xml.responseText = io.contentDocument.document.body?io.contentDocument.document.body.innerHTML:null;
                    xml.responseXML = io.contentDocument.document.XMLDocument?io.contentDocument.document.XMLDocument:io.contentDocument.document;
                }
            }catch(e)
            {
                jQuery.handleError(s, xml, null, e);
            }
            if ( xml || isTimeout == "timeout")
            {
                requestDone = true;
                var status;
                try {
                    status = isTimeout != "timeout" ? "success" : "error";
                    // Make sure that the request was successful or notmodified
                    if ( status != "error" )
                    {
                        // process the data (runs the xml through httpData regardless of callback)
                        var data = jQuery.uploadHttpData( xml, s.dataType );
                        // If a local callback was specified, fire it and pass it the data
                        if ( s.success )
                            s.success( data, status );

                        // Fire the global callback
                        if( s.global )
                            jQuery.event.trigger( "ajaxSuccess", [xml, s] );
                    } else
                        jQuery.handleError(s, xml, status);
                } catch(e)
                {
                    status = "error";
                    jQuery.handleError(s, xml, status, e);
                }

                // The request was completed
                if( s.global ) {
                    ///[bug:confict with yii.js(350)]@see http://www.lai18.com/content/9621987.html
                    var xhr = new XMLHttpRequest();
                    jQuery.event.trigger( "ajaxComplete", [xhr, s, xml] );
                }

                // Handle the global AJAX counter
                if ( s.global && ! --jQuery.active )
                    jQuery.event.trigger( "ajaxStop" );

                // Process result
                if ( s.complete )
                    s.complete(xml, status);

                jQuery(io).unbind()

                setTimeout(function()
                {	try
                    {
                        $(io).remove();
                        $(form).remove();

                    } catch(e)
                    {
                        jQuery.handleError(s, xml, null, e);
                    }

                }, 100)

                xml = null

            }
        }
        // Timeout checker
        if ( s.timeout > 0 )
        {
            setTimeout(function(){
                // Check to see if the request is still happening
                if( !requestDone ) uploadCallback( "timeout" );
            }, s.timeout);
        }
        try
        {
            // var io = $('#' + frameId);
            var form = $('#' + formId);
            $(form).attr('action', s.url);
            $(form).attr('method', 'POST');
            $(form).attr('target', frameId);
            if(form.encoding)
            {
                form.encoding = 'multipart/form-data';
            }
            else
            {
                form.enctype = 'multipart/form-data';
            }
            $(form).submit();

        } catch(e)
        {
            jQuery.handleError(s, xml, null, e);
        }
        if(window.attachEvent){
            document.getElementById(frameId).attachEvent('onload', uploadCallback);
        }
        else{
            document.getElementById(frameId).addEventListener('load', uploadCallback, false);
        }
        return {abort: function () {}};

    },

    uploadHttpData: function( r, type ) {
        var data = !type;
        data = type == "xml" || data ? r.responseXML : r.responseText;
        // If the type is "script", eval it in global context
        if ( type == "script" )
            jQuery.globalEval( data );
        // Get the JavaScript object, if JSON is used.
        if ( type == "json" )
        {
            // If you add mimetype in your response,
            // you have to delete the '<pre></pre>' tag.
            // The pre tag in Chrome has attribute, so have to use regex to remove
            var data = r.responseText;
            var rx = new RegExp("<pre.*?>(.*?)</pre>","i");
            var am = rx.exec(data);
            //this is the desired data extracted
            var data = (am) ? am[1] : "";    //the only submatch or empty
            eval( "data = " + data );
        }
        // evaluate scripts within html
        if ( type == "html" )
            jQuery("<div>").html(data).evalScripts();
        //alert($('param', data).each(function(){alert($(this).attr('value'));}));
        return data;
    }
})