function Point(x, y) {
	this.x = x;
	this.y = y;
}
Point.prototype = {
	relative: function(to) {
		return new Vector(to.x - this.x, to.y - this.y);
	},
	distance: function(to) {
		return Math.sqrt(Math.pow(this.x - to.x, 2) + Math.pow(this.y - to.y, 2));
	},
	add: function(other) {
		return new Vector(this.x + other.x, this.y + other.y);
	}
};
function Vector(x, y) {
	this.x = x;
	this.y = y;
}
Vector.prototype = {
	add: function(other) {
		return new Vector(this.x + other.x, this.y + other.y);
	},
	scale: function(by) {
		return new Vector(this.x * by, this.y * by);
	},
	normalize: function() {
		function norm(value) {
			return value > 0 ? 1 : value < 0 ? -1 : 0;
		}
		return new Vector(norm(this.x), norm(this.y));
	}
};
