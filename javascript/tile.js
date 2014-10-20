function tile(id) {

  this.id = id;
  this.frontColor = '#fcfcfc';
  this.backColor = '#fff';
  this.startAt = 1000;
  this.flipped = false;
  this.backContentImage = null;
  this.flipCompleteCallbacks = [];

  this.flip = function() {
    $("#" + this.id).html(this.getBackContent());
    $("#" +this.id+ " img").show('slide', {
      direction: 'up',
      complete: this.onFlipComplete()
    }, 5000);
    this.flipped = true;
  };

  this.onFlipComplete = function() {

    console.log("Flip complete");

    while(this.flipCompleteCallbacks.length > 0) {

      console.log("Running callback " + this.flipCompleteCallbacks[this.flipCompleteCallbacks.length - 1]);
      this.flipCompleteCallbacks[this.flipCompleteCallbacks.length - 1]();
      this.flipCompleteCallbacks.pop();
    }
  };

  this.revertFlip = function() {
    $("#" +this.id+ " img").hide('slide', {
        direction: 'up'
    }, 1000);

    this.flipped = false;
  };

  this.setBackContentImage = function(sBackContentImage) {
    this.backContentImage = sBackContentImage;
  };

  this.setTileId = function(sIdOfTile) {
    this.id = sIdOfTile;
  };

  this.setStartAt = function(iStartAt) {
    this.startAt = iStartAt;
  };

  this.setFrontColor = function(sColor) {
    this.frontColor = sColor;
  };

  this.setBackColor = function(sColor) {
    this.backColor = sColor;
  };

  this.getHTML = function() {
    return '<div id="' +this.id+ '" class="tile"></div>';
  };

  this.getStartAt = function() {
    return this.startAt;
  };

  this.getFlipped = function() {
    return this.flipped;
  };

  this.getBackContent = function() {
    return '<img src="' +this.backContentImage+ '"/>';
  };

  this.getBackContentImage = function() {
    return this.backContentImage;
  };

  this.addFlipCompleteCallback = function(callback) {
    this.flipCompleteCallbacks.push(callback);
  };
}
