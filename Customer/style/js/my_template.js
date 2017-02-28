/**
 * Created by PC on 2017-02-22.
 */
Vue.component('simple-grid', {
    template: '#my-template',
    props: ['newsList','noData','loading','noMore'],
    directives: {
        scroll: {
            bind: function (el, binding) {
                window.addEventListener('scroll', () => {
                    if (document.body.scrollTop + window.innerHeight >= el.clientHeight) {
                        demo.loadMore();
                    }
                })
            }
        }
    }
});
Vue.component('my-head', {
    template: '#head',
    props:['title']
});