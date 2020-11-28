module.exports =  function (element, context) {
    let isMouseDown = false;

    // initial mouse X and Y for `mousedown`
    let mouseX;
    let mouseY;

    // element X and Y before and after move
    let elementX = 0;
    let elementY = 0;

    // mouse button down over the element
    element.addEventListener('mousedown', onMouseDown);

    /**
     * Listens to `mousedown` event.
     *
     * @param {Object} event - The event.
     */
    function onMouseDown(event) {
        context.onMouseDown();
        mouseX = event.clientX;
        mouseY = event.clientY;
        isMouseDown = true;
        element.removeEventListener('mousedown', onMouseDown);
    }
    element.addEventListener('touchstart', function(e) {
        context.onMouseDown();
        let touchElement = e.changedTouches[0];

        mouseX = touchElement.clientX;
        mouseY = touchElement.clientY;
        isMouseDown = true;
        element.removeEventListener('touchstart', this)
    }, {passive: true});

    // mouse button released
    element.addEventListener('mouseup', onMouseUp);

    /**
     * Listens to `mouseup` event.
     *
     * @param {Object} event - The event.
     */
    function onMouseUp(event) {
        context.onMouseUp();
        elementX = 0;
        elementY = 0;
        isMouseDown = false;
        element.removeEventListener('mouseup', onMouseUp);
    }
    element.addEventListener('touchend', function(e) {
        context.onMouseUp();
        elementX = 0;
        elementY = 0;
        isMouseDown = false;
        element.removeEventListener('touchend', this)
    }, {passive: true});

    // need to attach to the entire document
    // in order to take full width and height
    // this ensures the element keeps up with the mouse
    document.addEventListener('mousemove', onMouseMove);

    /**
     * Listens to `mousemove` event.
     *
     * @param {Object} event - The event.
     */
    function onMouseMove(event) {
        if (!isMouseDown) return;
        var deltaX = event.clientX - mouseX;
        var deltaY = event.clientY - mouseY;
        element.style.left = elementX + deltaX + 'px';
        element.style.top = elementY + deltaY + 'px';

        context.onMouseMove();
    }
    element.addEventListener('touchmove', function(e) {
        if (!isMouseDown) return;
        var touchElement = e.changedTouches[0];

        let deltaX = touchElement.clientX - mouseX;
        let deltaY = touchElement.clientY - mouseY;
        element.style.left = elementX + deltaX + 'px';
        element.style.top = elementY + deltaY + 'px';

        context.onMouseMove();
    }, {passive: true});
};