var GLOBALSKIN = new Image();
var XC = 0;

var GLOBALCLOAK = new Image();
var CLC = 0;


function Skin(ElemId, Skin_Path, Cloak_Path, width, height) {
    GLOBALSKIN.onload = function() {
        XC = GLOBALSKIN.width / 64;
	CLC = GLOBALCLOAK.width > 22 ? 8 : 1; 
        SKIN3D();
    };
    GLOBALSKIN.src = Skin_Path;
    GLOBALCLOAK.src = Cloak_Path;
    
    cloak_path = Cloak_Path;
    elem_id = ElemId;
}

function SKIN3D() {
    var MSP = (function(global, undefined) {
        window.requestAnimFrame = (function() {
            return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame || function(callback, element) {
                window.setTimeout(callback, 1000 / 60);
            };
        })();
        var container = global.document.getElementById(elem_id);
        var width = 200, height = 380;
        var tileUvWidth = 1 / 1024;
        var tileUvHeight = 1 / 512;
        var SkinB = global.document.createElement('canvas');
        var sbc = SkinB.getContext('2d');
        var sR = 2;
        SkinB.width = 1024 * sR;
        SkinB.height = 512 * sR;
        var SkinCa = global.document.createElement('canvas');
        var skinc = SkinCa.getContext('2d');
        SkinCa.width = 1024;
        SkinCa.height = 512;
        var CloakCa = global.document.createElement('canvas');
        var Cloakc = CloakCa.getContext('2d');
        //CloakCa.width = 2048;
        //CloakCa.height = 1024;
        CloakCa.width = 1024;
        CloakCa.height = 512;
        var BGCANa = global.document.createElement('canvas');
        var BGCAN = BGCANa.getContext('2d');
        BGCANa.width = width;
        BGCANa.height = height;
        var GM = function(img, trans) {
            var material = new THREE.MeshBasicMaterial({
                map: new THREE.Texture(img, new THREE.UVMapping(), THREE.ClampToEdgeWrapping, THREE.ClampToEdgeWrapping, THREE.NearestFilter, THREE.NearestFilter, (trans ? THREE.RGBAFormat : THREE.RGBFormat)),
                transparent: trans
            });
            material.map.needsUpdate = true;
            return material;
        };
        var uvmap = function(mesh, face, x, y, w, h, rotateBy) {
            if (!rotateBy) rotateBy = 0;
            var uvs = mesh.geometry.faceVertexUvs[0][face];
            var tileU = x;
            var tileV = y;
            uvs[(0 + rotateBy) % 4].u = tileU * tileUvWidth;
            uvs[(0 + rotateBy) % 4].v = tileV * tileUvHeight;
            uvs[(1 + rotateBy) % 4].u = tileU * tileUvWidth;
            uvs[(1 + rotateBy) % 4].v = tileV * tileUvHeight + h * tileUvHeight;
            uvs[(2 + rotateBy) % 4].u = tileU * tileUvWidth + w * tileUvWidth;
            uvs[(2 + rotateBy) % 4].v = tileV * tileUvHeight + h * tileUvHeight;
            uvs[(3 + rotateBy) % 4].u = tileU * tileUvWidth + w * tileUvWidth;
            uvs[(3 + rotateBy) % 4].v = tileV * tileUvHeight;
        };

        var ChM = GM(SkinCa, false);
        var ChMTrans = GM(SkinCa, true);
        var CaM = GM(CloakCa, false);
        var BgMa = GM(BGCANa, false);
        var camera = new THREE.PerspectiveCamera(35, width / height, 1, 1000);
        camera.position.z = 50;
        var scene = new THREE.Scene();
        scene.add(camera);
        var HeadGl = new THREE.Object3D();
        var GBody = new THREE.Object3D();
        var Left1geo = new THREE.CubeGeometry(4, 12, 4);
        for (var i = 0; i < 8; i += 1) {
            Left1geo.vertices[i].y -= 6;
        }
        var Left1 = new THREE.Mesh(Left1geo, ChM);
        Left1.position.z = -2;
        Left1.position.y = -6;
        uvmap(Left1, 0, 8 * XC, 20 * XC, -4 * XC, 12 * XC);
        uvmap(Left1, 1, 16 * XC, 20 * XC, -4 * XC, 12 * XC);
        uvmap(Left1, 2, 4 * XC, 16 * XC, 4 * XC, 4 * XC, 3);
        uvmap(Left1, 3, 8 * XC, 20 * XC, 4 * XC, -4 * XC, 1);
        uvmap(Left1, 4, 12 * XC, 20 * XC, -4 * XC, 12 * XC);
        uvmap(Left1, 5, 4 * XC, 20 * XC, -4 * XC, 12 * XC);
        var Rightgeo = new THREE.CubeGeometry(4, 12, 4);
        for (var i = 0; i < 8; i += 1) {
            Rightgeo.vertices[i].y -= 6;
        }
        var Right = new THREE.Mesh(Rightgeo, ChM);
        Right.position.z = 2;
        Right.position.y = -6;
        uvmap(Right, 0, 4 * XC, 20 * XC, 4 * XC, 12 * XC);
        uvmap(Right, 1, 12 * XC, 20 * XC, 4 * XC, 12 * XC);
        uvmap(Right, 2, 8 * XC, 16 * XC, -4 * XC, 4 * XC, 3);
        uvmap(Right, 3, 12 * XC, 20 * XC, -4 * XC, -4 * XC, 1);
        uvmap(Right, 4, 0 * XC, 20 * XC, 4 * XC, 12 * XC);
        uvmap(Right, 5, 8 * XC, 20 * XC, 4 * XC, 12 * XC);
        var bodygeo = new THREE.CubeGeometry(4, 12, 8);
        var Body = new THREE.Mesh(bodygeo, ChM);
        uvmap(Body, 0, 20 * XC, 20 * XC, 8 * XC, 12 * XC);
        uvmap(Body, 1, 32 * XC, 20 * XC, 8 * XC, 12 * XC);
        uvmap(Body, 2, 20 * XC, 16 * XC, 8 * XC, 4 * XC, 1);
        uvmap(Body, 3, 28 * XC, 16 * XC, 8 * XC, 4 * XC, 3);
        uvmap(Body, 4, 16 * XC, 20 * XC, 4 * XC, 12 * XC);
        uvmap(Body, 5, 28 * XC, 20 * XC, 4 * XC, 12 * XC);
        GBody.add(Body);
        var Leftgeo = new THREE.CubeGeometry(4, 12, 4);
        for (var i = 0; i < 8; i += 1) {
            Leftgeo.vertices[i].y -= 4;
        }
        var Left = new THREE.Mesh(Leftgeo, ChM);
        Left.position.z = -6;
        Left.position.y = 4;
        Left.rotation.x = 0;
        uvmap(Left, 0, 48 * XC, 20 * XC, -4 * XC, 12 * XC);
        uvmap(Left, 1, 56 * XC, 20 * XC, -4 * XC, 12 * XC);
        uvmap(Left, 2, 48 * XC, 16 * XC, -4 * XC, 4 * XC, 1);
        uvmap(Left, 3, 52 * XC, 16 * XC, -4 * XC, 4 * XC, 3);
        uvmap(Left, 4, 52 * XC, 20 * XC, -4 * XC, 12 * XC);
        uvmap(Left, 5, 44 * XC, 20 * XC, -4 * XC, 12 * XC);
        GBody.add(Left);
        var Right2geo = new THREE.CubeGeometry(4, 12, 4);
        for (var i = 0; i < 8; i += 1) {
            Right2geo.vertices[i].y -= 4;
        }
        var Right2 = new THREE.Mesh(Right2geo, ChM);
        Right2.position.z = 6;
        Right2.position.y = 4;
        Right2.rotation.x = 0;
        uvmap(Right2, 0, 44 * XC, 20 * XC, 4 * XC, 12 * XC);
        uvmap(Right2, 1, 52 * XC, 20 * XC, 4 * XC, 12 * XC);
        uvmap(Right2, 2, 44 * XC, 16 * XC, 4 * XC, 4 * XC, 1);
        uvmap(Right2, 3, 48 * XC, 16 * XC, 4 * XC, 4 * XC, 3);
        uvmap(Right2, 4, 40 * XC, 20 * XC, 4 * XC, 12 * XC);
        uvmap(Right2, 5, 48 * XC, 20 * XC, 4 * XC, 12 * XC);
        GBody.add(Right2);
        var headgeo = new THREE.CubeGeometry(8, 8, 8);
        var Head = new THREE.Mesh(headgeo, ChM);
        Head.position.y = 2;
        uvmap(Head, 0, 8 * XC, 8 * XC, 8 * XC, 8 * XC);
        uvmap(Head, 1, 24 * XC, 8 * XC, 8 * XC, 8 * XC);
        uvmap(Head, 2, 8 * XC, 0 * XC, 8 * XC, 8 * XC, 1);
        uvmap(Head, 3, 16 * XC, 0 * XC, 8 * XC, 8 * XC, 3 * XC);
        uvmap(Head, 4, 0 * XC, 8 * XC, 8 * XC, 8 * XC);
        uvmap(Head, 5, 16 * XC, 8 * XC, 8 * XC, 8 * XC);
        HeadGl.add(Head);
        HeadGl.position.y = 8;
        var CloakO = new THREE.Object3D();
        //var CloakG = new THREE.CubeGeometry(1, 16, 10);
        var CloakG = new THREE.CubeGeometry(1, 16, 10);
        var Cloak = new THREE.Mesh(CloakG, CaM);
        Cloak.position.y = -8;
        Cloak.visible = false;
        
        uvmap(Cloak, 0, 1 * CLC, 1 * CLC, 10 * CLC, 16 * CLC); //перед
        uvmap(Cloak, 1, 12 * CLC, 1 * CLC, 10 * CLC, 16 * CLC); // зад
        uvmap(Cloak, 2, 1 * CLC, 0, 10 * CLC, 1 * CLC, 1); // верх
        uvmap(Cloak, 3, 11 * CLC, 0, 10 * CLC, 1 * CLC, 1); // низ
        uvmap(Cloak, 4, 0, 1 * CLC, 1 * CLC, 16 * CLC); // лево
        uvmap(Cloak, 5, 11 * CLC, 1 * CLC, 1 * CLC, 16 * CLC); // право
	
	//uvmap(Cloak, 0, 1 * 8, 1 * 8, 10 * 8, 16 * 8); //перед
        //uvmap(Cloak, 1, 12 * 8, 1 * 8, 10 * 8, 16 * 8); // зад
        //uvmap(Cloak, 2, 1 * 8, 0, 10 * 8, 1 * 8, 1); // верх
        //uvmap(Cloak, 3, 11 * 8, 0, 10 * 8, 1 * 8, 1); // низ
        //uvmap(Cloak, 4, 0, 1 * 8, 1 * 8, 16 * 8); // лево
        //uvmap(Cloak, 5, 11 * 8, 1 * 8, 1 * 8, 16 * 8); // право
		//uvmap(Cloak, 0, 1, 1, 10, 16);
        //uvmap(Cloak, 1, 12, 1, 10, 16);
        //uvmap(Cloak, 2, 1, 0, 10, 1);
        //uvmap(Cloak, 3, 11, 0, 10, 1, 1);
        //uvmap(Cloak, 4, 0, 1, 1, 16);
        //uvmap(Cloak, 5, 11, 1, 1, 16);
        CloakO.rotation.y = Math.PI;
        CloakO.position.x = -2;
        CloakO.position.y = 6;
        CloakO.rotation.z = 0.17;
        CloakO.add(Cloak);
        var PM = new THREE.Object3D();
        PM.add(Left1);
        PM.add(Right);
        PM.add(GBody);
        PM.add(HeadGl);
        PM.add(CloakO);
        PM.position.y = 6;
        var playerGroup = new THREE.Object3D();
        playerGroup.add(PM);
        scene.add(playerGroup);
        var mouseX = 0;
        var mouseY = 0.1;
        var originMouseX = 0;
        var originMouseY = 0;
        var rad = 0;
        var isMouseOver = false;
        var isMouseDown = false;
        var counter = 0;
        var firstRender = true;
        var startTime = Date.now();
        var pausedTime = 0;
        var render = function() {
            requestAnimFrame(render, renderer.domElement);
            var oldRad = rad;
            var time = (Date.now() - startTime) / 1000;
            rad = mouseX;
            if (mouseY > 500) {
                mouseY = 500;
            } else if (mouseY < -500) {
                mouseY = -500;
            }
            camera.position.x = -Math.cos(rad / (width / 2) + (Math.PI / 0.9));
            camera.position.z = -Math.sin(rad / (width / 2) + (Math.PI / 0.9));
            camera.position.y = (mouseY / (height / 2)) * 1.5 + 0.2;
            camera.position.setLength(70);
            camera.lookAt(new THREE.Vector3(0, 1.5, 0));
            counter += 0.01;
            Left.rotation.z = -Math.sin(time * 3) / 2;
            Right2.rotation.z = Math.sin(time * 3) / 2;
            Left1.rotation.z = Math.sin(time * 3) / 3;
            Right.rotation.z = -Math.sin(time * 3) / 3;
            playerGroup.position.y = -4;
            renderer.render(scene, camera);
        };
        var renderer = new THREE.WebGLRenderer({
            antialias: true
        });
        var threecanvas = renderer.domElement;
        renderer.setSize(width, height);
        container.appendChild(threecanvas);
        var onMouseMove = function(e) {
            if (isMouseDown) {
                mouseX = (e.pageX - threecanvas.offsetLeft - originMouseX);
                mouseY = (e.pageY - threecanvas.offsetTop - originMouseY);
            }
        };
        threecanvas.addEventListener('mousedown', function(e) {
            e.preventDefault();
            originMouseX = (e.pageX - threecanvas.offsetLeft) - rad;
            originMouseY = (e.pageY - threecanvas.offsetTop) - mouseY;
            isMouseDown = true;
            isMouseOver = true;
            onMouseMove(e);
        }, false);
        global.addEventListener('mouseup', function(e) {
            isMouseDown = false;
        }, false);
        global.addEventListener('mousemove', onMouseMove, false);
        threecanvas.addEventListener('mouseout', function(e) {
            isMouseOver = false;
        }, false);
        render();
        var skin = new Image();
        skin.onload = function() {
            skinc.clearRect(0, 0, 64, 32);
            skinc.drawImage(skin, 0, 0);
            var imgdata = skinc.getImageData(0, 0, 64, 32);
            var pixels = imgdata.data;
            sbc.clearRect(0, 0, SkinB.width, SkinB.height);
            sbc.save();
            var iOnColor = true;
            var ColorCA = [40, 0];
            var ColorMain = (ColorCA[0] + ColorCA[1] * 64) * 4;
            var iPeD = function(x, y) {
                if (pixels[(x + y * 64) * 4 + 0] !== pixels[ColorMain + 0] || pixels[(x + y * 64) * 4 + 1] !== pixels[ColorMain + 1] || pixels[(x + y * 64) * 4 + 2] !== pixels[ColorMain + 2] || pixels[(x + y * 64) * 4 + 3] !== pixels[ColorMain + 3]) {
                    return true;
                }
                return false;
            };
            for (var i = 32; i < 64; i += 1) {
                for (var j = 8; j < 16; j += 1) {
                    if (iPeD(i, j)) {
                        iOnColor = false;
                        break;
                    }
                }
                if (!iOnColor) {
                    break;
                }
            }
            if (!iOnColor) {
                for (var i = 40; i < 56; i += 1) {
                    for (var j = 0; j < 8; j += 1) {
                        if (iPeD(i, j)) {
                            iOnColor = false;
                            break;
                        }
                    }
                    if (!iOnColor) {
                        break;
                    }
                }
            }
            for (var i = 0; i < 64; i += 1) {
                for (var j = 0; j < 32; j += 1) {
                    if (iOnColor && ((i >= 32 && i < 64 && j >= 8 && j < 16) || (i >= 40 && i < 56 && j >= 0 && j < 8))) {
                        pixels[(i + j * 64) * 4 + 3] = 0;
                    }
                    sbc.fillStyle = 'rgba(' + pixels[(i + j * 64) * 4 + 0] + ', ' + pixels[(i + j * 64) * 4 + 1] + ', ' + pixels[(i + j * 64) * 4 + 2] + ', ' + pixels[(i + j * 64) * 4 + 3] / 255 + ')';
                    sbc.fillRect(i * sR, j * sR, sR, sR);
                }
            }
            sbc.restore();
            skinc.putImageData(imgdata, 0, 0);
            ChM.map.needsUpdate = true;
            ChMTrans.map.needsUpdate = true;
        };
        var cape = new Image();
        cape.onload = function() {
            Cloakc.clearRect(0, 0, 64, 32);
            Cloakc.drawImage(cape, 0, 0);
            CaM.map.needsUpdate = true;
            Cloak.visible = true;
        };
        skin.src = GLOBALSKIN.src;
        cape.src = cloak_path;
    }(this));
}