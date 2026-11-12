(function () {
  var post = document.querySelector(".content-card");

  if (!post) {
    return;
  }

  var rtlLanguages = ["ar", "he", "fa", "ur"];

  function setPostDirection(language) {
    var code = String(language || "")
      .toLowerCase()
      .split(/[-_|]/)[0];

    if (!code) {
      return;
    }

    var direction = rtlLanguages.indexOf(code) !== -1 ? "rtl" : "ltr";
    post.setAttribute("dir", direction);
  }

  function checkGoogleTranslateClasses() {
    var classes =
      String(document.documentElement.className) +
      " " +
      (document.body ? String(document.body.className) : "");

    if (/\btranslated-rtl\b/.test(classes)) {
      setPostDirection("ar");
    } else if (/\btranslated-ltr\b/.test(classes)) {
      setPostDirection("en");
    }
  }

  // Set the post's starting direction from the page language
  setPostDirection(document.documentElement.getAttribute("lang") || "en");

  // Detect a language chosen from the Google Translate dropdown
  document.addEventListener(
    "change",
    function (event) {
      var selected = event.target;

      if (
        selected &&
        selected.classList &&
        selected.classList.contains("goog-te-combo")
      ) {
        setPostDirection(selected.value);
      }
    },
    true,
  );

  // Also detect direction classes added by Google Translate
  var observer = new MutationObserver(checkGoogleTranslateClasses);

  observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ["class"],
  });

  if (document.body) {
    observer.observe(document.body, {
      attributes: true,
      attributeFilter: ["class"],
    });
  }
})();
