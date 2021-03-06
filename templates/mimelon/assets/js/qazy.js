function reveal() {
    for (var A = 0; A < view_elements.length; A++) {
        var q = 0, g = view_elements[A];
        do isNaN(g.offsetTop) || (q += g.offsetTop); while (g = g.offsetParent);
        var B = window.pageYOffset, Q = window.innerHeight, C = 0, g = view_elements[A];
        do isNaN(g.offsetLeft) || (C += g.offsetLeft); while (g = g.offsetParent);
        var I = window.pageXOffset, w = window.innerWidth;
        q > B && B + Q > q && C > I && I + w > C ? (view_elements[A].src = view_elements[A].getAttribute("data-qazy-src"), console.log(view_elements[A].src), view_elements.splice(A, 1), A--) : console.log("offsetParentTop" + q + " pageYOffset" + B + " viewportHeight" + window.innerHeight)
    }
}

function qazy_list_maker() {
    for (var A = document.querySelectorAll("img[data-qazy][data-qazy='true']"), q = 0; q < A.length; q++) {
        view_elements.push(A[q]), A[q].setAttribute("data-qazy", "false");
        var g = A[q].src;
        A[q].setAttribute("data-qazy-src", g), A[q].src = qazy_image
    }
}

var qazy_image = '/uploads/no-photo.png',
    view_elements = [];
window.addEventListener("resize", reveal, !1), window.addEventListener("scroll", reveal, !1);
var intervalObject = setInterval(function () {
    qazy_list_maker()
}, 50);
window.addEventListener("load", function () {
    clearInterval(intervalObject), qazy_list_maker(), reveal()
}, !1);