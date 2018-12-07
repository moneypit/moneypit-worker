var express = require('express');
var child_process = require('child_process');
const bodyParser = require("body-parser");
var router = express.Router();

router.get('/:workerName', function(req, res, next) {

  var rKey = config.resources.worker.redis_prefix + req.params.workerName;

  rClient.get(rKey, function(err, reply) {
    if (err) {
      res.json(err);
    } else {

      if (reply == null) {
        res.json({});
      } else {
        res.json(JSON.parse(reply));
      }

    }

  });




});

router.get('/', function(req, res, next) {
  workers = require('../data_files/workers.json');
  res.json(workers);
});

module.exports = router;
