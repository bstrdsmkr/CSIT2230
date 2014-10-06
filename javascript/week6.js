var difficulty = 10;
var target_fps = 60;
var canvas;
var all_blocks = new Array();
var level = 1;
var block_width = 50;
var block_height = 10;
var num_rows, num_cols;
var GRAVITY = new Vector(0, 9.81);
  var FRICTION = 0.85;
  var world = {
    x1: 0,
    y1: 0
  };
  $(window).resize(function() {
    world.x2 = $(window).width();
    world.y2 = $(window).height();
  }).trigger("resize");

function player(canvas){
  this.velocity = 0;
  this.speed = 1;
  this.x = 0;
  this.y = 0;
  this.width = 30;
  this.height = 5;
  this.draw = function draw(){
    right_limit = canvas.width() - (this.width/2);
    left_limit  = 0 + (this.width/2);
    this.x += this.velocity;
    this.x = Math.min(this.x, right_limit);
    this.x = Math.max(this.x, left_limit );
    canvas.drawRect({
      fillStyle: '#aaa',
      x: this.x,
      y: this.y,
      width: this.width,
      height: this.height
    });
  };
  this.move = function move(dir){
    right_limit = canvas.width() - (this.width/2);
    left_limit  = 0 + (this.width/2);
    if (this.x <= left_limit || this.x >= right_limit)
      this.velocity = 0;
    if (dir == 'left' )
      this.velocity -= this.speed;
    if (dir == 'right')
      this.velocity += this.speed;
  };
}

function block() {
  this.health = 1;
  this.width = 50;
  this.height = 10;

  this.col = getBoundedRandom(1, num_cols);
  this.row = getBoundedRandom(1, num_rows);
  this.x = (left_padding + (block_width  * this.col)) - (block_width/2);
  // console.log(left_padding);
  this.y = (top_padding  + (block_height * this.row));
  // console.log('Block: \n row: '+this.row+'\n col: '+this.col+'\n X: '+this.x+'\n Y: '+this.y);
  this.color = getRandomColor();
  this.draw = function draw(){
    canvas.drawRect({
      fillStyle: this.color,
      x: this.x,
      y: this.y,
      width: this.width,
      height: this.height,
      cornerRadius: 3
    });
  };
}

function ball(){
  this.loaded = true;
  this.x = player1.x;
  this.y = player1.y - 7;
  this.draw = function draw(){
    if (!loaded)
    canvas.drawArc({
      fillStyle: 'grey',
      x: this.x,
      y: this.y,
      radius: 5
    });
  };
}

function populateBoard(){
  var num_blocks = (difficulty*5) + (level*difficulty);
  for (i=0; i<=num_blocks; i++){
    all_blocks[i] = new block();
  }
}

function getBoundedRandom(min, max){
  return Math.floor(Math.random()*(max-min+1)) + min;
}

function getRandomColor(){
  return '#' + Math.floor(Math.random()*16777215).toString(16);
}

function main(){
  canvas.clearCanvas();
  player1.draw();
  the_ball.draw();
  all_blocks.forEach(function(this_block){
    this_block.draw();
  });
}

$(document).ready(function(){

  //cache the canvas lookup
  canvas = $("canvas");

  // Player stuff
  player1 = new player(canvas);
  player1.x = canvas.width()/2;
  player1.y = canvas.height() - 10;

  // Start with one ball
  the_ball = new ball();

  // Setup the board
  num_rows = Math.floor((canvas.height() - 80) / block_height);
  num_cols = Math.floor(canvas.width() / block_width);
  left_padding = (canvas.width() - (num_cols * block_width))/2;
  top_padding = (canvas.height() - (num_rows * block_height))/2;
  populateBoard();

  setInterval(main, (1000/target_fps));
});

$(document).keydown(function(e){
  var key = e.which;
       if(key == "37") player1.move('left');
  else if(key == "38") d = "up";
  else if(key == "39") player1.move('right');
  else if(key == "40") player1.velocity = 0;
});
