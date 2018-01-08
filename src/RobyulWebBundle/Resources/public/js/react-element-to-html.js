// Source: https://github.com/Anifacted/react-element-to-html

var ATTRIBUTES = {
  "accept": {
    "alias": "accept",
    "boolean": false
  },
  "acceptCharset": {
    "alias": "accept-charset",
    "boolean": false
  },
  "accessKey": {
    "alias": "accesskey",
    "boolean": false
  },
  "action": {
    "alias": "action",
    "boolean": false
  },
  "allowFullScreen": {
    "alias": "allowfullscreen",
    "boolean": true
  },
  "allowTransparency": {
    "alias": "allowtransparency",
    "boolean": false
  },
  "alt": {
    "alias": "alt",
    "boolean": false
  },
  "async": {
    "alias": "async",
    "boolean": true
  },
  "autoComplete": {
    "alias": "autocomplete",
    "boolean": false
  },
  "cellPadding": {
    "alias": "cellpadding",
    "boolean": false
  },
  "cellSpacing": {
    "alias": "cellspacing",
    "boolean": false
  },
  "charSet": {
    "alias": "charset",
    "boolean": false
  },
  "challenge": {
    "alias": "challenge",
    "boolean": false
  },
  "checked": {
    "alias": "checked",
    "boolean": true
  },
  "classID": {
    "alias": "classid",
    "boolean": false
  },
  "className": {
    "alias": "class",
    "boolean": false
  },
  "cols": {
    "alias": "cols",
    "boolean": false
  },
  "colSpan": {
    "alias": "colspan",
    "boolean": false
  },
  "content": {
    "alias": "content",
    "boolean": false
  },
  "contentEditable": {
    "alias": "contenteditable",
    "boolean": false
  },
  "contextMenu": {
    "alias": "contextmenu",
    "boolean": false
  },
  "controls": {
    "alias": "controls",
    "boolean": true
  },
  "coords": {
    "alias": "coords",
    "boolean": false
  },
  "crossOrigin": {
    "alias": "crossorigin",
    "boolean": false
  },
  "data": {
    "alias": "data",
    "boolean": false
  },
  "dateTime": {
    "alias": "datetime",
    "boolean": false
  },
  "defer": {
    "alias": "defer",
    "boolean": true
  },
  "dir": {
    "alias": "dir",
    "boolean": false
  },
  "disabled": {
    "alias": "disabled",
    "boolean": true
  },
  "download": {
    "alias": "download",
    "boolean": false
  },
  "draggable": {
    "alias": "draggable",
    "boolean": false
  },
  "encType": {
    "alias": "enctype",
    "boolean": false
  },
  "form": {
    "alias": "form",
    "boolean": false
  },
  "formAction": {
    "alias": "formaction",
    "boolean": false
  },
  "formEncType": {
    "alias": "formenctype",
    "boolean": false
  },
  "formMethod": {
    "alias": "formmethod",
    "boolean": false
  },
  "formNoValidate": {
    "alias": "formnovalidate",
    "boolean": true
  },
  "formTarget": {
    "alias": "formtarget",
    "boolean": false
  },
  "frameBorder": {
    "alias": "frameborder",
    "boolean": false
  },
  "headers": {
    "alias": "headers",
    "boolean": false
  },
  "height": {
    "alias": "height",
    "boolean": false
  },
  "hidden": {
    "alias": "hidden",
    "boolean": true
  },
  "high": {
    "alias": "high",
    "boolean": false
  },
  "href": {
    "alias": "href",
    "boolean": false
  },
  "hrefLang": {
    "alias": "hreflang",
    "boolean": false
  },
  "htmlFor": {
    "alias": "for",
    "boolean": false
  },
  "httpEquiv": {
    "alias": "http-equiv",
    "boolean": false
  },
  "icon": {
    "alias": "icon",
    "boolean": false
  },
  "id": {
    "alias": "id",
    "boolean": false
  },
  "inputMode": {
    "alias": "inputmode",
    "boolean": false
  },
  "keyParams": {
    "alias": "keyparams",
    "boolean": false
  },
  "keyType": {
    "alias": "keytype",
    "boolean": false
  },
  "label": {
    "alias": "label",
    "boolean": false
  },
  "lang": {
    "alias": "lang",
    "boolean": false
  },
  "list": {
    "alias": "list",
    "boolean": false
  },
  "loop": {
    "alias": "loop",
    "boolean": true
  },
  "low": {
    "alias": "low",
    "boolean": false
  },
  "manifest": {
    "alias": "manifest",
    "boolean": false
  },
  "marginHeight": {
    "alias": "marginheight",
    "boolean": false
  },
  "marginWidth": {
    "alias": "marginwidth",
    "boolean": false
  },
  "max": {
    "alias": "max",
    "boolean": false
  },
  "maxLength": {
    "alias": "maxlength",
    "boolean": false
  },
  "media": {
    "alias": "media",
    "boolean": false
  },
  "mediaGroup": {
    "alias": "mediagroup",
    "boolean": false
  },
  "method": {
    "alias": "method",
    "boolean": false
  },
  "min": {
    "alias": "min",
    "boolean": false
  },
  "minLength": {
    "alias": "minlength",
    "boolean": false
  },
  "multiple": {
    "alias": "multiple",
    "boolean": true
  },
  "muted": {
    "alias": "muted",
    "boolean": true
  },
  "name": {
    "alias": "name",
    "boolean": false
  },
  "noValidate": {
    "alias": "novalidate",
    "boolean": true
  },
  "open": {
    "alias": "open",
    "boolean": true
  },
  "optimum": {
    "alias": "optimum",
    "boolean": false
  },
  "pattern": {
    "alias": "pattern",
    "boolean": false
  },
  "placeholder": {
    "alias": "placeholder",
    "boolean": false
  },
  "poster": {
    "alias": "poster",
    "boolean": false
  },
  "preload": {
    "alias": "preload",
    "boolean": false
  },
  "radioGroup": {
    "alias": "radiogroup",
    "boolean": false
  },
  "readOnly": {
    "alias": "readonly",
    "boolean": true
  },
  "rel": {
    "alias": "rel",
    "boolean": false
  },
  "required": {
    "alias": "required",
    "boolean": true
  },
  "role": {
    "alias": "role",
    "boolean": false
  },
  "rows": {
    "alias": "rows",
    "boolean": false
  },
  "rowSpan": {
    "alias": "rowspan",
    "boolean": false
  },
  "sandbox": {
    "alias": "sandbox",
    "boolean": false
  },
  "scope": {
    "alias": "scope",
    "boolean": false
  },
  "scoped": {
    "alias": "scoped",
    "boolean": true
  },
  "scrolling": {
    "alias": "scrolling",
    "boolean": false
  },
  "seamless": {
    "alias": "seamless",
    "boolean": true
  },
  "selected": {
    "alias": "selected",
    "boolean": true
  },
  "shape": {
    "alias": "shape",
    "boolean": false
  },
  "size": {
    "alias": "size",
    "boolean": false
  },
  "sizes": {
    "alias": "sizes",
    "boolean": false
  },
  "span": {
    "alias": "span",
    "boolean": false
  },
  "spellCheck": {
    "alias": "spellcheck",
    "boolean": false
  },
  "src": {
    "alias": "src",
    "boolean": false
  },
  "srcDoc": {
    "alias": "srcdoc",
    "boolean": false
  },
  "srcSet": {
    "alias": "srcset",
    "boolean": false
  },
  "start": {
    "alias": "start",
    "boolean": false
  },
  "step": {
    "alias": "step",
    "boolean": false
  },
  "style": {
    "alias": "style",
    "boolean": false
  },
  "summary": {
    "alias": "summary",
    "boolean": false
  },
  "tabIndex": {
    "alias": "tabindex",
    "boolean": false
  },
  "target": {
    "alias": "target",
    "boolean": false
  },
  "title": {
    "alias": "title",
    "boolean": false
  },
  "type": {
    "alias": "type",
    "boolean": false
  },
  "useMap": {
    "alias": "usemap",
    "boolean": false
  },
  "value": {
    "alias": "value",
    "boolean": false
  },
  "width": {
    "alias": "width",
    "boolean": false
  },
  "wmode": {
    "alias": "wmode",
    "boolean": false
  },
  "wrap": {
    "alias": "wrap",
    "boolean": false
  }
};

function _typeof(obj) { return obj && obj.constructor === Symbol ? "symbol" : typeof obj; }

var VOID_ELEMENTS = ['area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'];

var escapeHtml = function escapeHtml(input) {
  return input.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#x27;");
};

var convert = function convert(element) {
  var type = element.type;

  if (!type) return element;

  var props = element.props;
  var children = props.dangerouslySetInnerHTML && props.dangerouslySetInnerHTML.__html || props.children;
  var renderedChildren = undefined;

  if (Array.isArray(children)) {
    renderedChildren = children.map(convert).join('');
  } else if (children && (typeof children === 'undefined' ? 'undefined' : _typeof(children)) === 'object') {
    renderedChildren = convert(children);
  } else {
    renderedChildren = children || '';
  }

  var attributes = Object.keys(element.props).reduce(function (result, key) {

    if (/^(data|aria)-.*/.test(key)) {

      result += ' ' + key + '="' + escapeHtml(String(element.props[key])) + '"';
    } else if (key !== 'children' && ATTRIBUTES[key] !== undefined) {

      var attrName = ATTRIBUTES[key] && ATTRIBUTES[key].alias || key;
      var attrValue = ATTRIBUTES[key].boolean ? '' : element.props[key];

      result += ' ' + attrName + '="' + attrValue + '"';
    }
    return result;
  }, '');

  if (~VOID_ELEMENTS.indexOf(type)) {
    return '<' + type + attributes + '/>';
  } else {
    return '<' + type + attributes + '>' + renderedChildren + '</' + type + '>';
  }
};
