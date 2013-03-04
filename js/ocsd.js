function show_tab(tab) {
  if (!tab.html()) {
    tab.load(tab.attr('data-target'));
  }
}

function init_tabs() {
  show_tab($('.tab-pane.active'));
  $('a[data-toggle="tab"]').on('show', function(e) {
    tab = $('#' + $(e.target).attr('href').substr(1));
    show_tab(tab);
  });
}

$(function () {
  init_tabs();
});