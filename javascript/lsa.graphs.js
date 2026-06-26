
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
    document.getElementById('baseArea').textContent = '';
    document.getElementById('counterpartArea').textContent = '';
    graph.forEachNode((node, attribute) => {
        graph.setNodeAttribute(node, 'size', 10);
        oldColor = graph.getNodeAttribute(node, 'orig_color');
        graph.setNodeAttribute(node, 'color', oldColor);
        nodeAbbrev = graph.getNodeAttribute(node, 'abbrev');
        graph.setNodeAttribute(node, 'label', nodeAbbrev);

        sigma.refresh();
    });
    graph.forEachEdge((edge, attribute) => {
        graph.setEdgeAttribute(edge, 'color', 'lightgray');

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

function showCounterparts() {
    // base ids
    let baseIdList = document.getElementById('baseArea').textContent;
    // console.log(baseIdList);
    // alert('baseIdList after buttonclick');
    if (baseIdList == '') {
        alert('Please right-click any node first to reveal its similar neighbors.');
        return;
    }
    // counterpart ids
    let counterpartIdList = document.getElementById('counterpartArea').textContent;
    // console.log(counterpartIdList);
    // alert('counterpartIdList after buttonclick');
    if (counterpartIdList == '') {
        alert('A base node is selected (maroon),so please click on a neighbor (indian red) to select a counterpart to view them side by side in a new tab.');
        return;
    }

    let baseIds = baseIdList.split(';');
    let baseId = baseIds[0];
    let baseIdInt = parseInt(baseId, 10);
    let baseDbid = baseIds[1];
    let baseDbidInt = parseInt(baseDbid, 10);
    
    let counterpartIds = counterpartIdList.split(';');
    let counterpartId = counterpartIds[0];
    let counterpartIdInt = parseInt(counterpartId, 10);
    let counterpartDbid = counterpartIds[1];
    let counterpartDbidInt = parseInt(counterpartDbid, 10);
    
    // weight (cosine between pair) and size (i.e. which chunk size and database, 'ch250' or 'ch1000')
    let edgeWeight = document.getElementById('weightArea').textContent;
    let size = document.getElementById('chunkSizeArea').textContent;

    // console.log('size: ' + size + ', baseDbid: ' + baseDbid);
    // console.log('counterpartDbid: ' + counterpartDbid + ', edgeWeight: ' + edgeWeight);
    // alert('parameters for request for side-by-side view');

    if (baseDbidInt < counterpartDbidInt) {
        openViewer(size, baseDbid, counterpartDbid, edgeWeight);
    } else {
        openViewer(size, counterpartDbid, baseDbid, edgeWeight);
    }
}

function releaseGraphMemory() {
    if (typeof window.sigma !== 'undefined') {
        // console.log('window.sigma is defined.');
        window.sigma.kill();
        window.sigma = null;
        // console.log('window.sigma instance was found and killed.');
    }
    // else {
    //     console.log('window.sigma was not found.');
    // }
    if (typeof window.userGraph !== 'undefined') {
        // console.log('window.userGraph is defined.');
        window.userGraph.clear();
        window.userGraph = null;
        // console.log('window.userGraph was found and killed.');
    }
    // else {
    //     console.log('window.userGraph === \'undefined\'.');
    // }
    // alert('exiting graph');
    location.reload();
}