// xAnimation r3, Copyright 2006-2007 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

function xAnimation(r)
{
  this.res = r||10;
}
xAnimation.prototype.init = function(e,t,or,ot,oe,a,b)
{
  var i = this;
  i.e = xGetElementById(e);
  i.t = t;
  i.or=or; i.ot=ot; i.oe=oe;
  i.a = a||0;
  i.v = xAnimation.vf[i.a];
  i.qc = 1 + (b||0);
  i.fq = 1/i.t;
  if (i.a) {
    i.fq *= i.qc * Math.PI;
    if (i.a == 1 || i.a == 2) { i.fq /= 2; }
  }
  else { i.qc = 1; }
  i.xd=i.x2-i.x1; i.yd=i.y2-i.y1; i.zd=i.z2-i.z1;
};
xAnimation.prototype.run = function(r)
{
  var i = this;
  if (!r) i.t1 = new Date().getTime();
  if (!i.tmr) i.tmr = setInterval(
    function() {
      i.et = new Date().getTime() - i.t1;
      if (i.et < i.t) {
        i.f = i.v(i.et*i.fq);
        i.x=i.xd*i.f+i.x1; i.y=i.yd*i.f+i.y1; i.z=i.zd*i.f+i.z1;
        i.or(i);
      }
      else {
        clearInterval(i.tmr); i.tmr = null;
        if (i.qc%2) {i.x=i.x2; i.y=i.y2; i.z=i.z2;}
        else {i.x=i.x1; i.y=i.y1; i.z=i.z1;}
        i.ot(i);
        var rep = false;
        if (typeof i.oe == 'function') rep = i.oe(i);
        else if (typeof i.oe == 'string') rep = eval(i.oe);
        if (rep) i.resume(1);
      }
    }, i.res
  );
};
xAnimation.vf = [
  function(r){return r;},
  function(r){return Math.abs(Math.sin(r));},
  function(r){return 1-Math.abs(Math.cos(r));},
  function(r){return (1-Math.cos(r))/2;}
];
xAnimation.prototype.pause = function()
{
  clearInterval(this.tmr);
  this.tmr = null;
};
xAnimation.prototype.resume = function(fs)
{
  if (typeof this.tmr != 'undefined' && !this.tmr) {
    this.t1 = new Date().getTime();
    if (!fs) {this.t1 -= this.et;}
    this.run(!fs);
  }
};
