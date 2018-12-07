var createError = require('http-errors');
var express = require('express');
var path = require('path');
var cookieParser = require('cookie-parser');
var logger = require('morgan');
var redis = require('redis');
var cors = require('cors')

var indexRouter = require('./routes/index');
var workersRouter = require('./routes/workers');
var poolDefinitionsRouter = require('./routes/pool_definitions');

require('dotenv').config()

// Load config
config = require('./config.json');

// Connect to redis
rClient = redis.createClient(config.redis);

// Load swagger file
swagger = require('./swagger.json');

var app = express();

// view engine setup
app.set('views', path.join(__dirname, 'views'));
// app.set('view engine', 'jade');

app.use(logger('dev'));
app.use(express.json());
app.use(express.urlencoded({ extended: false }));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'public')));

app.use('/api/', indexRouter);
app.use('/api/workers', workersRouter);
app.use('/api/pool_definitions', poolDefinitionsRouter);
app.use('/', express.static(path.join(__dirname, 'ui')))

// catch 404 and forward to error handler
app.use(function(req, res, next) {
  next(createError(404));
});

// error handler
app.use(function(err, req, res, next) {
  // set locals, only providing error in development
  res.locals.message = err.message;
  res.locals.error = req.app.get('env') === 'development' ? err : {};

  // render the error page
  res.status(err.status || 500);
  res.render('error');
});

module.exports = app;
