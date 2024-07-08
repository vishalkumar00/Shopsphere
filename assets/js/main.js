(function () {
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
    on('click', '.toggle-sidebar-btn', function (e) {
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

  var itemsMainDiv = ('.MultiCarousel');
  var itemsDiv = ('.MultiCarousel-inner');
  var itemWidth = 0;

  function ResCarouselSize() {
    var incno = 0;
    var dataItems = 'data-items';
    var itemClass = '.item';
    var id = 0;
    var btnParentSb = '';
    var itemsSplit = '';
    var sampwidth = $(itemsMainDiv).width();
    var bodyWidth = $('body').width();

    $(itemsDiv).each(function () {
      id = id + 1;
      var itemNumbers = $(this).find(itemClass).length;
      btnParentSb = $(this).parent().attr(dataItems);
      itemsSplit = btnParentSb.split(',');
      $(this).parent().attr('id', 'MultiCarousel' + id);

      if (bodyWidth >= 1200) {
        incno = itemsSplit[3];
        itemWidth = sampwidth / incno;
      } else if (bodyWidth >= 992) {
        incno = itemsSplit[2];
        itemWidth = sampwidth / 4;
      } else if (bodyWidth >= 768) {
        incno = itemsSplit[1];
        itemWidth = sampwidth / 3;
      } else if (bodyWidth >= 576) {
        incno = itemsSplit[0];
        itemWidth = sampwidth / 2;
      } else {
        incno = itemsSplit[0];
        itemWidth = sampwidth / 1;
      }

      $(this).css({ 'transform': 'translateX(0px)', 'width': itemWidth * itemNumbers });
      $(this).find(itemClass).each(function () {
        $(this).outerWidth(itemWidth);
      });

      $('.leftLst').addClass('over');
      $('.rightLst').removeClass('over');
    });
  }

  function ResCarousel(e, el, s) {
    var leftBtn = '.leftLst';
    var rightBtn = '.rightLst';
    var translateXval = '';
    var divStyle = $(el + ' ' + itemsDiv).css('transform');
    var values = divStyle.match(/-?[\d\.]+/g);
    var xds = Math.abs(values[4]);
    if (e === 0) {
      translateXval = parseInt(xds) - parseInt(itemWidth * s);
      $(el + ' ' + rightBtn).removeClass('over');

      if (translateXval <= itemWidth / 2) {
        translateXval = 0;
        $(el + ' ' + leftBtn).addClass('over');
      }
    } else if (e === 1) {
      var itemsCondition = $(el).find(itemsDiv).width() - $(el).width();
      translateXval = parseInt(xds) + parseInt(itemWidth * s);
      $(el + ' ' + leftBtn).removeClass('over');

      if (translateXval >= itemsCondition - itemWidth / 2) {
        translateXval = itemsCondition;
        $(el + ' ' + rightBtn).addClass('over');
      }
    }
    $(el + ' ' + itemsDiv).css('transform', 'translateX(' + -translateXval + 'px)');
  }

  function click(ell, ee) {
    var Parent = '#' + $(ee).parent().attr('id');
    var slide = $(Parent).attr('data-slide');
    ResCarousel(ell, Parent, slide);
  }

  ResCarouselSize();

  $(window).resize(function () {
    ResCarouselSize();
  });

  $('.leftLst').click(function () {
    click(0, this);
  });

  $('.rightLst').click(function () {
    click(1, this);
  });

  // Back to Top Button For User Portal 
  let backToTopBtn = document.getElementById("backToTopBtn");

  window.onscroll = function () {
    scrollFunction();
  };

  function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
      backToTopBtn.style.display = "block";
    } else {
      backToTopBtn.style.display = "none";
    }
  }

  backToTopBtn.onclick = function () {
    document.body.scrollTop = 0; 
    document.documentElement.scrollTop = 0; 
  };

  // Product image changing according to color circle hover 
  $('.pd-color-circle').hover(function() {
    var variantImage = $(this).data('variant-image');
    $(this).closest('.card').find('.card-img-top').attr('src', variantImage);
}, function() {
    var originalImage = $(this).closest('.card').find('.card-img-top').attr('src');
    $(this).closest('.card').find('.card-img-top').attr('src', originalImage);
});

// Scroll functinality 
$(window).scroll(function () {
  if ($(this).scrollTop() > 0) {
    $('#topNavbar').addClass('fixed-top-scroll');
  } else {
    $('#topNavbar').removeClass('fixed-top-scroll');
  }
});

})();
