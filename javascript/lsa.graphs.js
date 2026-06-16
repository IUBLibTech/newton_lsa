
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

        let weight = graph.getEdgeAttribute(edge, 'weight');
        weightFloat = parseFloat(weight);
        if (weightFloat >= 0.9) {
            weight_size = 6;
        }
        else if (weightFloat >= 0.8) {
            weight_size = 4;
        }
        else if (weightFloat >= 0.7) {
            weight_size = 3;
        }
        else if (weightFloat >= 0.6) {
            weight_size = 2;
        }
        else {
            weight_size = 1;
        }
        graph.setEdgeAttribute(edge, 'size', weight_size);
    });
}
	