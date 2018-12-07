new Vue({
  el: '#moneypit-worker',
  data: {
    device: '',
    location: '',
    workers: []
  },

  methods: {

  },

  created () {
    var vm = this;

    axios.get('./api/config')
    .then(function (response) {
      vm.device = response.data.device;
      vm.location = response.data.location;
    })

    axios.get('./api/workers')
    .then(function (response) {
      response.data.workers.forEach(function(v,k) {
        axios.get('./api/workers/' + v.name)
          .then(function (response) {
            console.log(response);
            vm.workers.push(response.data);
          })
      });
    })

  }
})
