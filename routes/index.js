var express = require('express');
var child_process = require('child_process');
const bodyParser = require("body-parser");
var router = express.Router();

router.get('/config', function(req, res, next) {
  res.json(config);
});

router.get('/', function(req, res, next) {
  res.json(swagger);
});

module.exports = router;
