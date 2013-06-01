/* ************************************************************************

 TestCenter Client - Simplified Functional/User Acceptance Testing

 Copyright:
 2012-2013 Paulo Ferreira <pf at sourcenotes.org>

 License:
 AGPLv3: http://www.gnu.org/licenses/agpl.html
 See the LICENSE file in the project's top-level directory for details.

 Authors:
 * Paulo Ferreira

 ************************************************************************ */

/* ************************************************************************

 ************************************************************************ */

qx.Class.define("tc.toaster.Manager", {
  extend: qx.core.Object,

  events: {
  },

  properties: {
    /** Maximum Number of Toast that can be visible at any one time */
    maxVisisble: {
      check: 'Integer',
      init: 5,
      nullable: false,
      apply: '_applyMaxVisible'
    },
    /** Default Delay for a Toast */
    delay: {
      check: 'Integer',
      init: 2000, // 2 Seconds
      nullable: false,
      apply: '_applyDelay',
      themeable: true
    },
    /** Toast Width */
    width: {
      check: 'Integer',
      init: 250,
      nullable: false,
      apply: '_applyWidth',
      themeable: true
    },
    /** Toast Height */
    height: {
      check: 'Integer',
      init: 100,
      nullable: false,
      apply: '_applyHeight',
      themeable: true
    },
    size: {
      group: ['width', 'height'],
      mode: 'shorthand'
    },
    /** Left or Right margin from the edge of the window */
    margin: {
      check: 'Integer',
      init: 20,
      nullable: false,
      apply: '_applyMargin',
      themeable: true
    },
    /** Left or Right margin from the edge of the window */
    spacing: {
      check: 'Integer',
      init: 10,
      nullable: false,
      apply: '_applyMargin',
      themeable: true
    },
    /** Controls whether the toast will display on the Left Side (TRUE) or the Right Side (FALSE) of the window */
    onLeft: {
      check: 'Boolean',
      init: false,
      nullable: false,
      apply: '_applyOnLeft',
      themeable: true
    },
    /** Controls whether the toast will drop from the Top (TRUE) or popup from the Bottom (FALSE) */
    fromTop: {
      check: 'Boolean',
      init: false,
      nullable: false,
      apply: '_applyFromBottom',
      themeable: true
    }
  },

  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */

  /**
   *
   */
  construct: function () {
    this.base(arguments);

    this.__popped = [];
    this.__toasting = [];
  },

  destruct: function () {
  },

  members: {
    __counter: 0,
    __popped: null,
    __toasting: null,

    /**
     *
     * @param message
     * @param properties
     * @return {*}
     */
    add: function (message, properties) {

      // Make sure we have a message
      message = tc.util.String.nullOnEmpty(message, false);
      if (message !== null) {
        // Create the Toast
        var toast = qx.lang.Object.mergeWith(
          {
            id: null,
            window: null,
            content: null,
            permanent: false,
            delay: this.getDelay()
          }, (properties == null) ? {} : properties, true);

        toast.content = message;

        // Put the toast on the Conveyor Belt
        return this.__burn(toast);
      }

      return null;
    },

    /**
     *
     * @param id
     * @return {Boolean}
     */
    remove: function (id) {
      // Note : JavaScript is single threaded

      // See if the Toast is in the Toaster
      var toast;
      for (var i = 0; i < this.__popped.length; ++i) {
        if (this.__toasting[i].id === id) {
          if (i === 0) { // At the beginning
            toast = this.__toasting.shift();
          } else if (i === (length - 1)) { // At the end
            toast = this.__toasting.pop();

          } else { // Some where in the middle
            toast = this.__toasting[i];
            // Remove Element from the Array
            this.__toasting = this.__toasting.slice(0, i).concat(this.__toasting.slice(i + 1));
          }
        }
      }

      if (toast === null) {
        // See if the Toast has Popped
        for (i = 0; i < this.__toasting; ++i) {
          if (this.__popped[i].id === id) {
            toast = this.__popped[i];
          }
        }
      }

      if (toast.window) {
        this.window.close();
      }

      return toast !== null;
    },

    _applyMaxVisible: function (value, old) {
    },
    _applyWidth: function (value, old) {
    },
    _applyHeight: function (value, old) {
    },
    _applyMargin: function (value, old) {
    },
    _applyOnLeft: function (value, old) {
    },
    _applyFromBottom: function (value, old) {
    },

    /**
     *
     * @param toast
     * @return {String}
     * @private
     */
    __burn: function (toast) {
      // Create Toast ID
      var id = 'id' + this.__counter++;
      toast.id = id;

      this.__toasting.push(toast);
      this.__pop();
      return id;
    },

    /**
     *
     * @private
     */
    __pop: function () {
      if (this.__toasting.length &&
        (this.__popped.length < this.getMaxVisisble())) { // We have a Toast and Space Available for it
        // Get the Root Windows Size
        var root = qx.core.Init.getApplication().getRoot();
        var rootSize = root.getSizeHint();

        // Get Starting Points
        var posStart = {
          left: this.getOnLeft() ? this.getMargin() : rootSize.width - this.getMargin() - this.getWidth(),
          top: this.getFromTop() ? this.getMargin() : rootSize.height - this.getMargin()
        };

        // GetFinal Position
        var posFinal = {
          left: posStart.left,
          top: posStart.top
        };
        if (this.getFromTop()) {
          posFinal.top += this.__popped.length * (this.getSpacing() + this.getHeight());
          if ((posFinal.top + this.getHeight()) > rootSize.height) { // Out of Bound (So Don't Pop the Toast)
            return false;
          }
        } else {
          posFinal.top -= (this.__popped.length + 1) * (this.getSpacing() + this.getHeight());

          if (posFinal.top < 0) { // Out of Bound (So Don't Pop the Toast)
            return false;
          }
        }

        if (this.getOnLeft()) {
          if ((posFinal.left + this.getWidth()) > rootSize.width) { // Out-of-Bounds
            return false;
          }
        } else {
          if (posFinal.left < this.getMargin()) { // Out of Bounds
            return false;
          }
        }

        // Everything Check out, so pop the toast

        // Get the Toast
        var toast = this.__toasting.shift();
        this.__popped.push(toast);

        // Create the Window
        var win = toast.window = new qx.ui.window.Window();
        win.setWidth(this.getWidth());
        win.setHeight(this.getHeight());
        win.setShowMinimize(false);
        win.setShowMaximize(false);
        win.setMovable(false);
        win.setResizable(false, false, false, false);
        win.setOpacity(0);
        win.setLayout(new qx.ui.layout.VBox(0));
        win.addListenerOnce('close', function (e) {
          this.__fadeOut(win);
          this.__consume(toast);
        }, this);
        win.addListenerOnce("appear", function (e) {
          this.__verticalSlideIn(win, (posStart.top - posFinal.top));
        }, this);

        // Create the Content
        var embed1 = new qx.ui.embed.Html(toast.content);
        embed1.setOverflow("auto", "auto");
        embed1.setWidth(250);
        embed1.setHeight(50);
        embed1.setDecorator("main");
        win.add(embed1);

        // Add the Window to the Root
        root.add(win, posFinal);
        win.open();
      }
    },

    __verticalSlideIn: function (win, distance) {
      qx.bom.element.Animation.animate(win.getContainerElement().getDomElement(), {
        duration: this.getDelay(),
        delay: 200,
        timing: 'ease-out',
        keyFrames: {
          // 1st Frame: Translate the Window to the Bottom of the Screen
          0: {  opacity: 0, translate: ["0px", distance + "px"] },
          // Last Frame: Translation is 0 (i.e. the window ends up at the original position)
          100: { opacity: 1, translate: ["0px", "0px"] }
        }
      }).addListenerOnce('end', function (e) {
          win.setOpacity(1);
        });
    },

    __verticalShift: function (win, distance) {
      return qx.bom.element.Animation.animate(win.getContainerElement().getDomElement(), {
        duration: this.getDelay(),
        delay: 200,
        timing: 'ease-out',
        keyFrames: {
          // 1st Frame: Translate the Window to the Bottom of the Screen
          0: {  opacity: 1, translate: ["0px", distance + "px"] },
          // Last Frame: Translation is 0 (i.e. the window ends up at the original position)
          50: { opacity: 0, translate: ["0px", "0px"] },
          // 1st Frame: Translate the Window to the Bottom of the Screen
          100: {  opacity: 1, translate: ["0px", distance + "px"] }
        }
      });
    },

    __fadeOut: function (win) {
      qx.bom.element.Animation.animate(win.getContainerElement().getDomElement(), {
        duration: 5000,
        delay: 200,
        keep: 100,
        timing: 'ease-in',
        keyFrames: {
          0: { opacity: 1},
          100: { opacity: 0, display: "none" }
        }
      });
    },

    __consume: function (toast) {

      // Find the Toast's Index
      for (var i = 0; i < this.__popped.length; ++i) {
        if (this.__popped[i] === toast) {
          break;
        }
      }

      // Process the toast
      if (i === (this.__popped.length - 1)) { // At the end -- No Movement Required
        toast = this.__popped.pop();
      } else if (i < this.__popped.length) { // Beginning or Middle - Some movement required

        var shift = this.getHeight() + this.getMargin();
        shift = this.getFromTop() ? -shift : shift;

        // Move the Toast
        var win, bounds;
        for (var j = i + 1; j < this.__popped.length; ++j) {
          win = this.__popped[i].window;
          bounds = win.getBounds();
          this.__verticalShift(win, shift).addListenerOnce('end', function (e) {
            win.moveTo(bounds.left, bounds.top + shift);
          });
        }

        // Eat a Toast
        if (i === 0) { // At the beginning
          toast = this.__popped.shift();
        } else if (i < this.__popped.length) { // Some where in the middle
          toast = this.__popped[i];
          // Remove Element from the Array
          this.__popped = this.__popped.slice(0, i).concat(this.__popped.slice(i + 1));
        }
      } else {
        // TODO : Problem the toast has already been eaten
        return false;
      }

      return true;
    }
  }
});


/** Concept
 * - Fixed Size Windows that Float Up from the Bottom.
 * - Can contain any type of HTML Text.
 * - Can be auto closed (time delayed) or manual close (or both)
 * - have to animate
 *
 * Toast Properties
 * - Permanent - Timed
 *
 */

/* Example Playground Code
 var win = new qx.ui.window.Window();
 win.setWidth(300);
 win.setHeight(100);
 win.setShowMinimize(false);
 win.setShowMaximize(false);
 win.setLayout(new qx.ui.layout.VBox(0));

 this.getRoot().add(win, {left:40, top:40});

 var html1 =
 "<div style='background-color: white; text-align: center;'>" +
 "<i style='color: red;'><b>H</b></i>" +
 "<b>T</b>" +
 "<u>M</u>" +
 "<i>L</i>" +
 " Text" +
 "<br>" +
 " Text" +
 "</div>";
 var embed1 = new qx.ui.embed.Html(html1);
 embed1.setOverflow("auto", "auto");
 embed1.setWidth(250);
 embed1.setHeight(50);
 embed1.setDecorator("main");
 win.add(embed1);

 var button = new qx.ui.form.Button("animate");

 button.addListener("execute",function(e) {
 var container = win.getContainerElement();
 qx.bom.element.Animation.animate(container.getDomElement(),
 {
 duration: 1000, keyFrames :
 {
 0:   { opacity: 0, translate: ["0px", "200px"] },
 100: { opacity: 1, translate: ["0px", "0px"] }
 }
 }, undefined);
 });

 this.getRoot().add(button, {left:0, top:0});
 win.open();
 */