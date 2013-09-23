var po = org.polymaps;

var map = po.map()
    .container(document.getElementById("map").appendChild(po.svg("svg")))
    .add(po.interact())
    .add(po.hash());

map.add(po.image()
    .url(po.url("http://spaceclaw.stamen.com/toner/{Z}/{X}/{Y}.png")
    .hosts(["a.", "b.", "c.", ""])));

map.add(po.compass()
    .pan("none"));
