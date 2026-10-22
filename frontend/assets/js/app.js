$(document).ready(function () {

  const app = $.spapp({
    defaultView: "home",
    templateDir: "views/"
  });

  app.route({ view: "home", load: "home.html" });
  app.route({ view: "products", load: "products.html" });
  app.route({ view: "categories", load: "categories.html" });
  app.route({ view: "reviews", load: "reviews.html" });
  app.route({ view: "contact", load: "contact.html" });
  app.route({ view: "login", load: "login.html" });
  app.route({ view: "register", load: "register.html" });
  app.route({ view: "profile", load: "profile.html" });
  app.route({ view: "dashboard", load: "dashboard.html" });
  app.route({ view: "orders", load: "orders.html" });

  app.run();

});

// M1: biljezimo promjenu prikaza bez pozivanja backend servisa.
$(document).on("viewchange", function (e, page) {
  console.log("Loaded view:", page);
});

// M1: forme su samo staticki prikaz.
// Podaci se ne salju niti spremaju.
$(document).on("submit", "#spapp form", function (e) {
  e.preventDefault();

  if (!this.reportValidity()) {
    return;
  }

  let message = $(this).find(".m1-demo-message");

  if (message.length === 0) {
    message = $("<div>")
      .addClass("m1-demo-message alert alert-info mt-3")
      .attr("role", "status");

    $(this).append(message);
  }

  message.text(
    "Milestone 1 demo: this form is not connected to the backend yet."
  );
});