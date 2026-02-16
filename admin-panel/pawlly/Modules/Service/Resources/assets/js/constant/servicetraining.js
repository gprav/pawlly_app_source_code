export const MODULE = 'service-training'

export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'POST'}}
export const DURATIN_LIST = (id) => {return {path: `service-training/type-duration-list/${id}`, method: 'GET'}}