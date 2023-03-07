import React, { useRef } from 'react';
import App from './app';
import {createRoot} from "react-dom/client";

const root = createRoot(document.getElementById('homeDesign'));

root.render(
    <React.StrictMode>
       <App />
    </React.StrictMode>
);