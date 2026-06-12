
function downloadGraph(contents, filename) {
    const graphBlob = new Blob([contents], { type: 'text/plain' });
    const graphUrl = URL.createObjectURL(graphBlob);
    const graphLink = document.createElement('a');
    graphLink.href = graphUrl;
    graphLink.download = filename;
    document.body.appendChild(graphLink);
    graphLink.click();
    document.body.removeChild(graphLink);
    URL.revokeObjectURL(graphUrl);
}

function updateNodeAttribute(graph, sigma, nodeId, attrib, newValue) {
    graph.setNodeAttribute(nodeId, attrib, newValue);
    sigma.refresh();
}

function updateEdgeAttribute(graph, sigma, edgeId, attrib, newValue) {
    graph.setEdgeAttribute(edgeId, attrib, newValue);
    sigma.refresh();
}

function restoreGraph(graph, sigma) {
    let graphQueryElement = document.getElementById('queryNode');
    graphQueryElement.value = 'None';
    graph.forEachNode((node, attribute) => {
        graph.setNodeAttribute(node, 'size', 10);
        oldColor = graph.getNodeAttribute(node, 'orig_color');
        graph.setNodeAttribute(node, 'color', oldColor);
        nodeAbbrev = graph.getNodeAttribute(node, 'abbrev');
        graph.setNodeAttribute(node, 'label', nodeAbbrev);

        sigma.refresh();
    });
    graph.forEachEdge((edge, attribute) => {
        graph.setEdgeAttribute(edge, 'color', 'gray');
    });
}
