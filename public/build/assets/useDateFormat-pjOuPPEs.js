function r(e){const t=new Date(e);if(isNaN(t))return"";const n=("0"+t.getDate()).slice(-2),a=("0"+(t.getMonth()+1)).slice(-2),o=t.getFullYear();return`${n}-${a}-${o}`}export{r as f};
