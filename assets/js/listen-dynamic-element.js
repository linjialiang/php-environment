/**
 * js监听动态生成的元素 - 事件委托
 * @param {String} eventType js事件类型 click、keyup等
 * @param {String} element 非动态生成的父级元素
 * @param {String} selector 当前需要监听的动态元素
 * @param {Object} fn 函数
 */
function on(eventType, element, selector, fn) {
  if (!(element instanceof Element)) {
    element = document.querySelector(element);
  }
  element.addEventListener(eventType, (e) => {
    const t = e.target;
    if (t.matches(selector)) {
      fn(e);
    }
  });
}

// 运行
on('click', '#div1', 'button', (element) => {
  console.log('button 被点击了');
});
