/*
Add the function to the public prototype :
*/
sigma.publicPrototype.outDegreeToSize = function() {
  this.iterNodes(function(node){
    node.size = node.outDegree;
  }).draw();
};

/*
Of course, it works as well with node.degree and node.inDegree.

How to test it:
1. Open any sigma example - http://sigmajs.org/examples.html
2. Open a console         - http://bit.ly/GVshi6
3. Enter the previous code
4. Try it on the first sigma.js instance on the page:
     sigma.instances[1].outDegreeToSize();

Also, if you want to choose the minimum/maximum node sizes:
     sigma.instances[1].graphProperties({
       minNodeSize: 1,
       maxNodeSize: 3
     });
*/