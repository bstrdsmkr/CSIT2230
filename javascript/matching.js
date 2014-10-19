var tiles = [];
var flips = new Array('tb', 'bt', 'lr', 'rl' );
var iFlippedTile = null;
var iTileBeingFlippedId = null;
var tileImages = new Array(1,2,3,4,5,6,7,8);
var tileAllocation = null;
var iTimer = 0;
var iInterval = 100;
var iPeekTime = 3000;
var timePlayed = 0;
var timerId = null;
var score = 0;

function getRandomImageForTile() {

  var iRandomImage = Math.floor((Math.random() * tileAllocation.length)),
    iMaxImageUse = 2;

  while(tileAllocation[iRandomImage] >= iMaxImageUse ) {

    iRandomImage = iRandomImage + 1;

    if(iRandomImage >= tileAllocation.length) {

      iRandomImage = 0;
    }
  }

  return iRandomImage;
}

function createTile(iCounter) {

  var curTile =  new tile("tile" + iCounter),
    iRandomImage = getRandomImageForTile();

  tileAllocation[iRandomImage] = tileAllocation[iRandomImage] + 1;

  curTile.setFrontColor("tileColor" + Math.floor((Math.random() * 5) + 1));
  curTile.setStartAt(500 * Math.floor((Math.random() * 5) + 1));
  curTile.setFlipMethod(flips[Math.floor((Math.random() * 3) + 1)]);
  curTile.setBackContentImage("images/" +  (iRandomImage + 1) + ".jpg");

  return curTile;
}

function initState() {

  /* Reset the tile allocation count array.  This
    is used to ensure each image is only
    allocated twice.
  */
  tileAllocation = new Array(0,0,0,0,0,0,0,0);

  //kill all the tile objs
  while(tiles.length > 0) {
    tiles.pop();
  }

  $('#board').empty();
  iTimer = 0;

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

  var iCounter = 0;

  for(iCounter = 0; iCounter < tiles.length; iCounter++) {

    tiles[iCounter].revertFlip();

  }

  callback();
}

function revealTiles(callback) {

  var iCounter = 0,
    bTileNotFlipped = false;

  for(iCounter = 0; iCounter < tiles.length; iCounter++) {

    if(tiles[iCounter].getFlipped() === false) {

      if(iTimer > tiles[iCounter].getStartAt()) {
        tiles[iCounter].flip();
      }
      else {
        bTileNotFlipped = true;
      }
    }
  }

  iTimer = iTimer + iInterval;

  if(bTileNotFlipped === true) {
    setTimeout("revealTiles(" + callback + ")", iInterval);
  } else {
    callback();
  }
}

function checkMatch() {

  if(iFlippedTile === null) {

    iFlippedTile = iTileBeingFlippedId;

  } else {

    if( tiles[iFlippedTile].getBackContentImage() !== tiles[iTileBeingFlippedId].getBackContentImage()) {

      setTimeout("tiles[" + iFlippedTile + "].revertFlip()", 2000);
      setTimeout("tiles[" + iTileBeingFlippedId + "].revertFlip()", 2000);

    } else {
      score++;
      $('#score').html('Score: ' +score);
    }

    iFlippedTile = null;
    iTileBeingFlippedId = null;
  }
}

function onPeekComplete() {

  $('div.tile').click(function() {

    iTileBeingFlippedId = this.id.substring("tile".length);

    if(tiles[iTileBeingFlippedId].getFlipped() === false) {
      tiles[iTileBeingFlippedId].addFlipCompleteCallback(function() { checkMatch(); });
      tiles[iTileBeingFlippedId].flip();
    }

  });
}

function onPeekStart() {
  setTimeout("hideTiles( function() { onPeekComplete(); })", iPeekTime);
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

    setTimeout("revealTiles(function() { onPeekStart(); })", iInterval);

  });
});
