var app = new Vue({
    el: '#app',
    data: function () {
        return {
            udt: null,
            scheme: {
                name: 'new_scheme',
                type: 'state_machine',
                marking_store: {
                    type: '',
                    property: 'marking'
                },
                supports: '[App\\NewModel::class]',
                places: [],
                transitions: [],
            },

            form: {
                place: {
                    id: '',
                },
                transition: {
                    id: null,
                    from: [],
                    to: [],
                    modeEdit: false
                },
                editPlaceIndex: null,
                editTransitionIndex: null,
            },

            picture: '',
            php: '',
            tabActive: 'main'
        }
    },
    mounted: function () {

    },
    methods: {
        addPlace: function (index) {
            let self = this;
            let data = {
                id: self.form.place.id
            };

            if (self.form.place.id) {

                if (index === null) {
                    self.scheme.places.push(data);
                } else {
                    let oldId = self.scheme.places[index].id;
                    //Если переименовываем плейс, ищем связанные транзишины и переименовываем их
                    self.scheme.transitions.forEach(function (transition, t) {

                        transition.from.forEach(function(from, i) {
                            console.log('from === oldId')
                            console.log(from, oldId)
                            if (from === oldId) {
                                self.scheme.transitions[t].from[i] = self.form.place.id;
                            }
                        });
                        transition.to.forEach(function(to, i) {
                            console.log('to === oldId')
                            console.log(to, oldId)
                            if (to === oldId) {
                                self.scheme.transitions[t].to[i] = self.form.place.id;
                            }
                        });
                    });

                    self.scheme.places[index].id = self.form.place.id;

                    self.refresh();
                }

                self.setDefaultValuePlace();
            }
        },
        addTransition: function (index) {
            let data = {
                id: this.form.transition.id,
                from: this.form.transition.from,
                to: (Array.isArray(this.form.transition.to) ? this.form.transition.to : [this.form.transition.to])
            }

            if (this.form.transition.id && this.form.transition.from && this.form.transition.to) {
                if(index === null) {
                    this.scheme.transitions.push(data);
                } else {
                    this.scheme.transitions[index] = data;
                    this.refresh();
                }

                this.setDefaultValueTransition();
            }
        },

        refresh: function () {
            let self = this;
            axios.post('/', this.scheme)
                .then(function (response) {
                    self.picture = response.data.path;
                    self.php = response.data.php;
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        placeEdit: function(index) {
            this.form.editPlaceIndex = index;
            this.form.place = Object.assign({}, this.scheme.places[index]);

            this.tab('place');
        },
        transitionEdit: function(index) {
            this.form.editTransitionIndex = index;
            this.form.transition = this.scheme.transitions[index];

            this.tab('transition');
        },
        placeEditCancel: function() {
            this.setDefaultValuePlace();
        },
        transitionEditCancel: function() {
            this.setDefaultValueTransition();
        },
        tab: function (value) {
            this.tabActive = value;
        },
        setDefaultValuePlace: function () {
            this.form.place = {
                id: null,
            };

            this.form.editPlaceIndex = null;
        },
        setDefaultValueTransition: function () {
            this.form.transition = {
                id: null,
                from: [],
                to: [],
            };

            this.form.editTransitionIndex = null;
        }
    },
    computed: {
        update: function () {
            let a = this.scheme;
            return `${a.places}|${a.type}|${a.marking_store.property}|${a.supports}|${a.transitions}|${a.name}`
        }
    },
    watch: {
        update: function () {
            console.log('refresh');
            this.udt = Date.now();
            this.refresh()
        },

    }
});
