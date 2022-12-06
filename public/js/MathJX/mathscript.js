(function () {
    var V = MathJax.version.split(/\./);
    if (V[0] === "2" && (parseInt(V[1]) < 7 || (V[1] === "7" && parseInt(V[2]) <= 8))) {
      MathJax.Hub.Register.StartupHook("Collapsible Ready", function () {
        var Collapsible = MathJax.Extension.collapsible;
        Collapsible._MakeAction = Collapsible.MakeAction;
        Collapsible.MakeAction = function (collapse, mml) {
          if (mml.type !== 'math') return this._MakeAction(collapse, mml);
          var mrow = mml.data[0]; mml.data = mrow.data; mrow.data = [];
          var maction = this._MakeAction(collapse, mml);
          mrow.data = mml.data; mml.data = [mrow];
          return maction;
        };
      });
    }
  })();