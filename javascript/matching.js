var tiles = [];
var flippedTile = null;
var currTileID = null;
var tileImages = new Array(1,2,3,4,5,6,7,8);
var tileAllocation = null;
var timer = 0;
var flipInterval = 100;
var peekTime = 3000;
var timePlayed = 0;
var timerId = null;
var score = 0;
var clickGate = 0;


function getRandomImageForTile() {
  var idx = Math.floor((Math.random() * tileAllocation.length));
  var occurances = 2;

  while(tileAllocation[idx] >= occurances) {
    idx++;
    if(idx >= tileAllocation.length) {
      idx = 0;
    }
  }
  return idx;
}

function createTile(tile_idx) {

  var this_tile =  new tile("tile" + tile_idx);
  var img_idx = getRandomImageForTile();

  tileAllocation[img_idx]++;
  this_tile.setStartAt(500 * Math.floor((Math.random() * 5) + 1));
  this_tile.setBackContentImage("../images/" +(img_idx + 1)+ ".jpg");

  return this_tile;
}

function initState() {
  tileAllocation = new Array(0,0,0,0,0,0,0,0);

  //kill all the tile objs
  while(tiles.length > 0) {
    tiles.pop();
  }

  $('#board').empty();
  timer = 0;

}

function initTiles() {

  initState();

  // Randomly create 16 tiles and render to board
  for(i = 0; i < 16; i++) {

    var curTile = createTile(i);
    $('#board').append(curTile.getHTML());
    tiles.push(curTile);
  }
}

function hideTiles(callback) {
  for(i = 0; i < tiles.length; i++) {
    tiles[i].revertFlip();
  }
  callback();
}

function revealTiles(callback) {
  var bTileNotFlipped = false;
  for(i = 0; i < tiles.length; i++) {
    if(tiles[i].getFlipped() === false) {
      if(timer > tiles[i].getStartAt()) {
        tiles[i].flip();
      }
      else {
        bTileNotFlipped = true;
      }
    }
  }

  timer += flipInterval;

  if(bTileNotFlipped === true) {
    setTimeout("revealTiles(" + callback + ")", flipInterval);
  } else {
    callback();
  }
}

function checkMatch() {
  if(flippedTile === null) {
    flippedTile = currTileID;

  } else {
    if( tiles[flippedTile].getBackContentImage() !== tiles[currTileID].getBackContentImage()) {
      setTimeout("tiles[" + flippedTile + "].revertFlip()", 2000);
      setTimeout("tiles[" + currTileID + "].revertFlip()", 2000);

    } else {
      score++;
      $('#score').html('Score: ' +score);
    }

    flippedTile = null;
    currTileID = null;
  }
}

function onPeekComplete() {
  $('div.tile').click(function() {
    if (clickGate < 2){
      clickGate++;
      setTimeout(function(){clickGate--;}, 3000);
      currTileID = this.id.substring("tile".length);
      if(tiles[currTileID].getFlipped() === false) {
        tiles[currTileID].addFlipCompleteCallback(function() { checkMatch(); });
        tiles[currTileID].flip();
      }
    }
  });
}

function onPeekStart() {
  setTimeout("hideTiles( function() { onPeekComplete(); })", peekTime);
}

function startTimer(){
  timePlayed = new Date();
  timerId = setInterval(function(){
    var now = new Date();
    var delta = now - timePlayed;
    $('#timer').html('Time: ' +Math.round(delta/1000)+ 's');
    if (score == 8) {
      clearInterval(timerId);
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
        top: $(window).height()/2
      });
      return;
    }
  },1000);
}

$(document).ready(function() {

  $('#startGameButton').click(function() {

    initTiles();
    startTimer();

    setTimeout("revealTiles(function() { onPeekStart(); })", flipInterval);

  });
});
