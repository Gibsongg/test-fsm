var app = new Vue({
    el: '#app',
    data: function () {
        return {
            workflow: []
        }
    },
    mounted: function () {
        this.update();
    },
    methods: {
        update: function() {
            let self = this;
            axios.get('/?action=viewer')
                .then(function (response) {
                    console.log(response.data)
                    self.workflow = response.data;
                })
        }
    }
});
