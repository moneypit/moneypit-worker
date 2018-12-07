var express = require('express');
var child_process = require('child_process');
const bodyParser = require("body-parser");
var router = express.Router();

router.get('/', function(req, res, next) {
  pool_definitions = require('../data_files/pool_definitions.json');
  res.json(pool_definitions);
});

module.exports = router;
