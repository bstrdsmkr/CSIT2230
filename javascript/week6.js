var difficulty = 1;
var target_fps = 60;
var all_blocks = [];
var level = 1;
var block_width = 50;
var block_height = 10;
var num_rows, num_cols;
var num_blocks = (difficulty*5) + (level*difficulty);
var balls = 3;
var score = 0;
var STOPPED = new Vector(0, 0);
var GRAVITY = new Vector(0, 9.81);
var FRICTION = 0.85;
var hud = $("#hud");

var world = {
  x1: 0,
  y1: 0
};

$(window).resize(function() {
  world.x2 = $(window).width();
  world.y2 = $(window).height();
}).trigger("resize");


var QT = new QuadTree({
                x: 0,
                y: 0,
                width:  world.x2,
                height: world.y2
             });

function Player(){
  var x = world.x2/2;    // start out in the middle
  var y = world.y2 - 40; // 40px from the bottom
  this.position = new Point(x, y);
  this.output = $("<div>").addClass("player").appendTo("body");
  this.velocity = STOPPED;
  this.width = 30;
  this.height = 5;
  this.speed  = 5;
  this.type = "player";
}

Player.prototype = {
  update: function() {

    this.position = this.position.add(this.velocity);

    // if we've gone too far, stop and
    // clamp the position between 0 and the width of the world
    maxRight = world.x2 - (this.width);
    if (this.position.x >= maxRight) {
      this.velocity = STOPPED;
      this.position.x = maxRight;
    } else if (this.position.x <= 0){
      this.velocity = STOPPED;
      this.position.x = 0;
    }
    this.render();
  },
  center: function(){
    var halfx = this.position.x + (this.width/2);
    var halfy = this.position.y + (this.height/2);
    return new Point(halfx, halfy);
  },
  render: function() {
    // render
    this.output.css({
      position:   "absolute",
      left:       this.position.x,
      top:        world.y2 - 40,
      width:      this.width,
      height:     this.height,
      background: "grey"
    });
  }
};

function Block() {
  this.width = 50;
  this.height = 10;
  var maxX = world.x2 - this.width;
  var maxY = world.y2 - (this.height + 50);
  var x = getBoundedRandom(1, maxX);
  var y = getBoundedRandom(1, maxY);
  this.position = new Point(x, y);
  this.output = $("<div>").addClass("block").appendTo("body");
  this.velocity = STOPPED;
  this.health = 1;
  this.color = getRandomColor();
  this.type = "block";
}

Block.prototype = {
  remove: function() {
    this.output.remove();
  },
  hit: function(){
    this.health--;
    if (!this.health) this.remove();
  },
  render: function() {
    this.output.css({
      position:   "absolute",
      left:       this.position.x,
      top:        this.position.y,
      width:      block_width,
      height:     block_height,
      background: this.color,
      boxShadow: "0px 0px 5px #fff"
    });
  },
  update: function(){
    this.render();
  },
  center: function(){
    var halfx = this.position.x + (this.width/2);
    var halfy = this.position.y + (this.height/2);
    return new Point(halfx, halfy);
  }
};

function Ball(){
  this.position = new Point(world.x2/2, world.y2/2);
  this.output = $("<div>").addClass("ball").appendTo("body");
  this.width  = 5;
  this.height = 5;
  this.speed  = 3;
  var rx = getBoundedRandom(-this.speed, this.speed);
  this.velocity = new Vector(rx, this.speed);
  this.loaded = true;
  this.type = "ball";
}

Ball.prototype = {
  update: function(){
    this.position = this.position.add(this.velocity);

    // if we've gone too far, stop and
    // clamp the position between 0 and the width of the world
    maxRight = world.x2 - (this.width);
    if (this.position.x >= maxRight || this.position.x <= 0) {
      this.velocity.x = -this.velocity.x;
      this.position.x = Math.max(Math.min(this.position.x, maxRight), 0);
    }

    if (this.position.y <= 0){
      this.velocity.y = -this.velocity.y;
      this.position.y = 0;
    } else if (this.position.y >= world.y2){
      balls--;
      var rx = getBoundedRandom(-this.speed, this.speed);
      this.velocity = new Vector(rx, this.speed);
      this.position = new Point(world.x2/2, world.y2/2);
    }
    this.render();
  },
  render: function(){
    this.output.css({
      position:     "absolute",
      left:          this.position.x,
      top:           this.position.y,
      borderRadius:  5,
      background:   "white",
      width:         this.width,
      height:        this.height
    });
  },
  center: function(){
    var halfx = this.position.x + (this.width/2);
    var halfy = this.position.y + (this.height/2);
    return new Point(halfx, halfy);
  }
};

function rectContainsPoint(rTL, rBR, point){
  console.log('checking point '+point.x+','+point.y);
  return point.x >= rTL.x &&
         point.x <= rBR.x &&
         point.y >= rTL.y &&
         point.y <= rBR.y;
}

function populateBoard(){
  for (i=0; i<=num_blocks; i++){
    var new_block = new Block();
    new_block.idx = i;
    all_blocks.push(new_block);
  }
}

function getBoundedRandom(min, max){
  return Math.floor(Math.random()*(max-min+1)) + min;
}

function getRandomColor(){
  return '#' + Math.floor(Math.random()*16777215).toString(16);
}

function detectCollision() {
  var objects = [];
  QT.getAllObjects(objects);
  for (var x = 0, len = objects.length; x < len; x++) {
    QT.findObjects(obj = [], objects[x]);
    for (y = 0, length = obj.length; y < length; y++) {
      // DETECT COLLISION ALGORITHM
      switch(objects[x].type){
        case "block":
        case "player":
          break;
        case "ball":
          switch(objects[y].type){
            case "player":
              var w = 0.5 * (objects[x].width + objects[y].width);
              var h = 0.5 * (objects[x].height + objects[y].height);
              var dx = objects[x].center().x - objects[y].center().x;
              var dy = objects[x].center().y - objects[y].center().y;
              if (Math.abs(dx) <= w && Math.abs(dy) <= h){
                // ball bouncing off the paddle
                objects[x].velocity.y = -objects[x].velocity.y;
                // apply "spin" to the ball
                var transfer = new Vector(objects[y].velocity.x/2, 0);
                objects[x].velocity = objects[x].velocity.add(transfer);

              }
              break;
            case "block":
              var w = 0.5 * (objects[x].width + objects[y].width);
              var h = 0.5 * (objects[x].height + objects[y].height);
              var dx = objects[x].center().x - objects[y].center().x;
              var dy = objects[x].center().y - objects[y].center().y;

              if (Math.abs(dx) <= w && Math.abs(dy) <= h){
                // collision between ball and block
                var wy = w*dy;
                var hx = h*hx;

                if (wy > hx){
                  if (wy > -hx){    // ball struck the top
                    // bounce up
                    objects[x].velocity.y = -objects[x].velocity.y;
                    score++;
                    //kill the block
                    all_blocks.splice(objects[y].idx, 1);
                    objects[y].output.remove();
                  } else {          // ball struck the left side
                    //bounce right
                    objects[x].velocity.x = -objects[x].velocity.x;
                    score++;
                    //kill the block
                    all_blocks.splice(objects[y].idx, 1);
                    objects[y].output.remove();
                  }
                } else {
                  if (wy > -hx){    // ball struck the right side
                    // bounce left
                    objects[x].velocity.x = -objects[x].velocity.x;
                    score++;
                    //kill the block
                    all_blocks.splice(objects[y].idx, 1);
                    objects[y].output.remove();
                  } else {          // ball struck the bottom
                    // bounce down
                    objects[x].velocity.y = -objects[x].velocity.y;
                    score++;
                    //kill the block
                    all_blocks.splice(objects[y].idx, 1);
                    objects[y].output.remove();
                  }
                }
              }
              break;
          }
          break;
      }
    }
  }
}

function main(){
  QT.clear();
  QT.insert(p1);
  QT.insert(the_ball);
  QT.insert(all_blocks);
  detectCollision();
  p1.update();
  the_ball.update();
  all_blocks.forEach(function(this_block){
    this_block.update();
  });
  if (all_blocks.length == 1){
    populateBoard();
  }
  hud.text("Balls: " +balls+ " Score: " +score);
}

$(document).ready(function(){
  // Show the instructions
  $('#instructions').modal();
  // Player stuff
  p1 = new Player();
  p1.render();

  // Create a ball
  the_ball = new Ball();
  the_ball.render();

  // Setup the board
  num_rows = Math.floor((world.y2 - 80) / block_height);
  num_cols = Math.floor(world.x2 / block_width);
  left_padding = (world.x2 - (num_cols * block_width))/2;
  top_padding = (world.y2 - (num_rows * block_height))/2;
  populateBoard();
  $('#instructions').on('hidden.bs.modal', function(e){
    var game_loop = setInterval(function(){
      main();
      if (!balls){
        clearInterval(game_loop);
        var game_over_btn = $("<button type='button'>").appendTo("body");
        game_over_btn.addClass("game_over_btn");
        game_over_btn.addClass("btn");
        game_over_btn.addClass("btn-danger");
        game_over_btn.addClass("btn-block");
        game_over_btn.addClass("btn-lg");
        game_over_btn.text("Game Over");
        game_over_btn.click(function(){location.reload();});
        game_over_btn.css({
          position: "absolute",
          top: world.y2/2
        });
        return;
      }
    }, (1000/target_fps));
  });
});

$(document).keydown(function(e){
  var key = e.which;
       if(key == "37") p1.velocity = p1.velocity.add(new Vector(-p1.speed, 0));
  else if(key == "39") p1.velocity = p1.velocity.add(new Vector(p1.speed, 0));
  else if(key == "40") p1.velocity = STOPPED;
});
