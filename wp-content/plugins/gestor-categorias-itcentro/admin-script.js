jQuery(function ($) {
  // Toggle individual para activar/desactivar
  $(document).on("change", ".toggle", function () {
    const item = $(this).closest(".cat-item");
    const isActive = $(this).is(":checked");

    item.toggleClass("inactive", !isActive);

    $.post(itc_ajax.ajax_url, {
      action: "itc_toggle_category",
      id: item.data("id"),
      active: isActive,
      nonce: itc_ajax.nonce,
    });
  });

  // Buscador
  $("#itc-search").on("keyup", function () {
    const v = $(this).val().toLowerCase().trim();
    // Este selector funciona para cualquier .itc-card en la página
    $(".itc-grid .itc-card").each(function () {
      const name = $(this).find(".cat-name").text().toLowerCase();
      $(this).toggle(name.includes(v));
    });
  });
});
