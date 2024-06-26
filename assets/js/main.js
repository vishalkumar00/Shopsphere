(function() {
  "use strict";

  const select = (el, all = false) => {
    el = el.trim();
    if (all) {
      return [...document.querySelectorAll(el)];
    } else {
      return document.querySelector(el);
    }
  };

  const on = (type, el, listener, all = false) => {
    if (all) {
      select(el, all).forEach(e => e.addEventListener(type, listener));
    } else {
      select(el, all).addEventListener(type, listener);
    }
  };

  /**
   * Sidebar toggle
   */
  if (select('.toggle-sidebar-btn')) {
    on('click', '.toggle-sidebar-btn', function(e) {
      select('body').classList.toggle('toggle-sidebar');
    });
  }

  /**
   * Initialize sidebar state based on screen width
   */
  const initializeSidebar = () => {
    if (window.innerWidth >= 1200) {
      select('body').classList.remove('toggle-sidebar');
    } else {
      select('body').classList.add('toggle-sidebar');
    }
  };

  // Run on page load
  initializeSidebar();

  // Run on window resize
  window.addEventListener('resize', initializeSidebar);

    /**
   * Initiate Datatables
   */
    const datatables = select('.datatable', true)
    datatables.forEach(datatable => {
      new simpleDatatables.DataTable(datatable, {
        perPageSelect: [5, 10, 15, ["All", -1]],
        columns: [{
            select: 2,
            sortSequence: ["desc", "asc"]
          },
          {
            select: 3,
            sortSequence: ["desc"]
          },
          {
            select: 4,
            cellClass: "green",
            headerClass: "red"
          }
        ]
      });
    })
    
})();
