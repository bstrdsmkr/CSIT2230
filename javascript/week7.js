var all_kids = [];
var kid_imgs = ['kid1.png', 'kid2.jpg', 'kid3.png'];
var score = 0;
var TO_RADIANS = Math.PI/180;
var FPS = 60;
var STOPPED = new Vector(0, 0);
var num_kids = 10;
var hit_sound = new Audio('../audio/uggh.mp3');
var brake_sound = new Audio('../audio/brakes.mp3');

var world = {
  x1: 0,
  y1: 0
};

$(window).resize(function() {
  world.x2 = $(window).width();
  world.y2 = $(window).height();
}).trigger("resize");

function kid(){
  var img_num = getBoundedRandom(1, 5);
  var startx  = getBoundedRandom(0, world.x2);
  var starty  = getBoundedRandom(0, world.y2);
  var xvec    = getBoundedRandom(1,5);
  var yvec    = getBoundedRandom(1,5);
  var rand_img = kid_imgs[Math.floor(Math.random()*kid_imgs.length)];
  this.src = '../images/' + rand_img;
  this.position = new Point(startx, starty);
  this.velocity = new Vector(xvec, yvec);
  this.output = $("<img>").addClass("kid")
                          .attr('src', this.src)
                          .appendTo("body");
}

kid.prototype = {
  update: function(){
    var maxx = world.x2 - this.output.width();
    var maxy = world.y2 - this.output.height();
    this.position = this.position.add(this.velocity);

    if (this.position.x <= 0 || this.position.x >= maxx){
      this.velocity.x = -this.velocity.x;
    } else if (this.position.y <= 0 || this.position.y >= maxy){
      this.velocity.y = -this.velocity.y;
    }

    var maxx = world.x2 - this.output.width();
    var maxy = world.y2 - this.output.height();
    this.position.x = Math.min(maxx, Math.max(this.position.x, 0));
    this.position.y = Math.min(maxy, Math.max(this.position.y, 0));

    this.render();

    this.check_collisions();
  },
  render: function(){
    this.output.css({
      position: 'absolute',
      top: this.position.y,
      left: this.position.x
    });
  },
  check_collisions: function(){
    if (this.position.x < p1.position.x + p1.output.width() &&
        this.position.x + this.output.width() > p1.position.x &&
        this.position.y < p1.position.y + p1.output.height() &&
        this.position.y + this.output.height() > p1.position.y){
          // collision happened
          hit_sound.play();
          score++;
          var idx = all_kids.indexOf(this);
          all_kids.splice(idx, 1);
          this.output.remove();
          p1.velocity.x /= 2;
          p1.velocity.y /= 2;
        }
  }
};

function player() {
  this.src = '../images/car.png';
  this.position = new Point(world.x2/2, world.y2/2);
  this.velocity = STOPPED;
  this.speed = 0;
  this.acceleration = 4;
  this.handling = 16;
  this.rotation = 350;
  this.drag = 0.98;
  this.max_speed = 4;
  this.reverse_speed = 1.1;
  this.output = $("<img>").addClass("player")
                          .attr('src', this.src)
                          .appendTo("body");}

player.prototype = {
  is_moving: function(){
    return !(this.speed > -0.4 && this.speed < 0.4);
  },
  accelerate: function(){
    if (this.speed < this.max_speed){
           if (this.speed  <  0){this.speed *= this.drag;}
      else if (this.speed === 0){this.speed = 0.4;}
         else {this.speed *= this.acceleration;}
    }
  },
  decelerate: function(min){
    brake_sound.play();
    min = min || 0;
    if (Math.abs(this.speed) < this.max_speed){
      this.speed *= this.drag;
      this.speed = this.speed < min ? min : this.speed;
    } else if (this.speed === 0){
      this.speed = -0.4;
    } else {
      this.speed *= this.reverse_speed;
      this.speed = this.speed > min ? min : this.speed;
    }
  },
  turn_left: function(){
    if (this.is_moving()){
      this.rotation -= this.handling * (this.speed/this.max_speed);
    }
  },
  turn_right: function(){
    if (this.is_moving()){
      this.rotation += this.handling * (this.speed/this.max_speed);
    }
  },
  update: function(){
    this.velocity = new Vector(
          Math.sin(this.rotation * TO_RADIANS) * this.speed,
          Math.cos(this.rotation * TO_RADIANS) * this.speed * -1
    );
    this.position = this.position.add(this.velocity);
    this.position.x = Math.min(world.x2, Math.max(this.position.x, 0));
    this.position.y = Math.min(world.y2, Math.max(this.position.y, 0));
    this.render();
  },
  render: function(){
    this.output.css({
      transform: 'rotate('+this.rotation+'deg)',
      position: 'absolute',
      top: this.position.y,
      left: this.position.x
    });
  }
};

function getBoundedRandom(min, max){
  return Math.floor(Math.random()*(max-min+1)) + min;
}

function main(){
  // console.log('calling main');
  all_kids.forEach(function(this_kid){
    // console.log('updating');
    this_kid.update();
  });
  p1.update();
  $('#hud').html('Score: '+score);
}

function repop(){
  if (all_kids.length < 10){
    all_kids.push(new kid());
  }
}

$(document).ready(function(){
  // Show the instructions
  $('#instructions').modal();

  for (i=0; i<num_kids; i++){
    all_kids[i] = new kid();
  }
  p1 = new player();
  $('#instructions').on('hidden.bs.modal', function(e){
    setInterval(repop, (10000));
    var game_loop = setInterval(function(){
      if (all_kids.length === 0){
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
      main();
    }, (1000/FPS));
  });
});

$(document).keydown(function(e){
  var key = e.which;
       if(key == "37") p1.turn_left();
  else if(key == "39") p1.turn_right();
  else if(key == "40") p1.decelerate();
  else if(key == "38") p1.accelerate();
});
