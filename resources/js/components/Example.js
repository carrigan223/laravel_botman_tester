import React from "react";
import ReactDOM from "react-dom";

function Example() {
    return (
        <div
            style={{
                width: "100%",
                height: "100%",
                position: "absolute",
                left: "0",
                overflow: "hidden",
                background: "black",
                color: "white",
            }}
        >
            <h1>this whole screen is a react component</h1>
            <div
                style={{
                    width: "30vw",
                    height: "60vh",
                    background: "white",
                    color: "black",
                    position: "fixed",
                    bottom: "20px",
                    right: "600px",
                    zIndex: 2147483647,
                    borderRadius: "5px",
                    boxSizing: "content-box",
                    boxShadow: "0px 0px 20px rgba(0, 0, 0, 0.2)",
                    overflow: "hidden",
                }}
            >
                <h2>this div is still react</h2>
            </div>
        </div>
    );
}

export default Example;

if (document.getElementById("example")) {
    ReactDOM.render(<Example />, document.getElementById("example"));
}
