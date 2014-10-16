<?php
Header("content-type: application/x-javascript");
?>

(function() {

  // Have we already defined a Test Center Object?
  if (!window.$$tc)
    window.$$tc = {}; // NO

  /*
   * Create a Configuration Structure
   */
  window.$$tc.config = {
    'default': {
      ssl: false,
      server: '10.193.0.201',
      port: null,
      base_url: 'testcenter'
    },
    client: {
      base_url: 'testcenter/client'
    },
    services: {
      meta: {
        base_url: 'testcenter/services/meta'
      },
      web: {
        base_url: 'testcenter/services/web'
      }
    }
  };

  /*
   * TEST FUNCTIONS
   */
  window.$$tc.isset = function(value) {
    return (typeof (value) !== "undefined") && (value !== null);
  };
  window.$$tc.is_object = function(value, test_defined) {
    test_defined = !test_defined;
    // Test if value is defined (as well)?
    if (test_defined) { // YES
      return $$tc.isset(value) && (typeof (value) === "object");
    } else { //NO
      return typeof (value) === "object";
    }
  };

  /*
   * UTILITY FUNCTIONS
   */
  window.$$tc.basic_clone = function(source, deep) {
    // Initialize Flags
    deep = !!deep;

    // Do we have a source object?
    if (!$$tc.is_object(source)) {
      return source;
    }

    // Create a Destination Object
    var destination = {};

    // Loop Copying Entries
    for (var property in source) {
      // Is it a Local Property of the Source Object?
      if (!source.hasOwnProperty(property)) { // NO
        continue;
      }

      /* Conditions:
       * 1. Are we doing a deep copy?
       * 2. Is the Source Property an Object?
       */
      if (deep &&
        $$tc.is_object(source[property], false)) { // YES
        destination[property] = $$tc.basic_clone(source[property], true);
        continue;
      }
      // ELSE: Simple Copy
      destination[property] = source[property];
    }

    return destination;
  };

  window.$$tc.mixin = function(destination, source, overwrite, deep) {
    // Initialize Flags
    overwrite = !!overwrite;
    deep = !!deep;

    // Do we have a destination object?
    if (!$$tc.is_object(destination)) { // NO: Just Return a Clone of the Source
      return $$tc.isset(source) ? $$tc.basic_clone(source, deep) : {};
    }

    // Do we have a source object?
    if (!$$tc.is_object(source)) { // NO: Simply Return the Destination
      return destination;
    }

    // Loop Copying Entries
    for (var property in source) {
      // Is it a Local Property of the Source Object?
      if (!source.hasOwnProperty(property)) { // NO
        continue;
      }

      /* Conditions:
       * 1. Does the Destination already Have the Property Defined?
       * 2. Are we allowed to Overwrite it?
       */
      if (destination.hasOwnProperty(property) && !overwrite) { // NO
        continue;
      }

      /* Conditions:
       * 1. Are we doing a deep copy?
       * 2. Does the Destination Property Exist and Is an Object?
       * 3. Is the Source Property an Object?
       */
      if (deep &&
        $$tc.is_object(destination[property]) &&
        $$tc.is_object(source[property], false)) { // YES
        destination[property] = $$tc.mixin(destination[property], source[property], true);
        continue;
      }
      // ELSE: Simple Copy
      destination[property] = source[property];
    }

    return destination;
  };

  window.$$tc.__create_url = function(config, url_postfix) {
    var url = 'http' + (($$tc.isset(config.ssl) && config.ssl) ? 's://' : '://') + config.server;
    if ($$tc.isset(config.port)) {
      url += ':' + config.port;
    }
    url += '/' + config.base_url;
    if ($$tc.isset(url_postfix)) {
      url += '/' + url_postfix;
    }

    return url;
  };

  /*
   * URL Functions
   */
  window.$$tc.url_client = function() {
    // Is the Web Services Configuration Initialized?
    var base = $$tc.config.client;
    /* Conditions:
     * 1. Do we have Configuration Object for the URL?
     * 2. Is the URL Configuration Object Ready?
     */
    if (!$$tc.is_object(base) ||
      !base.hasOwnProperty('ready') ||
      !base.ready) { // NO
      base = $$tc.mixin(base, $$tc.config.default, false, true);

      // Configuration Structure Ready : URL is not
      base.ready = false;
    }
    
    // Have we already created a Web Services URL?
    if (!base.ready) { // NO
      base.url = $$tc.__create_url(base);
      base.ready = $$tc.isset(base.url);
      $$tc.config.client = base;
    }

    return base.url;
  };
  window.$$tc.url_meta_services = function() {
    // Is the Web Services Configuration Initialized?
    var base = $$tc.config.services.meta;
    /* Conditions:
     * 1. Do we have Configuration Object for the URL?
     * 2. Is the URL Configuration Object Ready?
     */
    if (!$$tc.is_object(base) ||
      !base.hasOwnProperty('ready') ||
      !base.ready) { // NO
      base = $$tc.mixin(base, $$tc.config.default, false, true);

      // Configuration Structure Ready : URL is not
      base.ready = false;
    }
    
    // Have we already created a Web Services URL?
    if (!base.ready) { // NO
      base.url = $$tc.__create_url(base);
      base.ready = $$tc.isset(base.url);
      $$tc.config.services.meta = base;
    }

    return base.url;
  };
  window.$$tc.url_web_services = function(url_postfix) {
    // Is the Web Services Configuration Initialized?
    var base = $$tc.config.services.web;
    /* Conditions:
     * 1. Do we have Configuration Object for the URL?
     * 2. Is the URL Configuration Object Ready?
     */
    if (!$$tc.is_object(base) ||
      !base.hasOwnProperty('ready') ||
      !base.ready) { // NO
      base = $$tc.mixin(base, $$tc.config.default, false, true);

      // Configuration Structure Ready : URL is not
      base.ready = false;
    }
    
    // Have we already created a Web Services URL?
    if (!base.ready) { // NO
      base.url = $$tc.__create_url(base);
      base.ready = $$tc.isset(base.url);
      $$tc.config.services.web = base;
    }

    return base.url;
  };
})();
