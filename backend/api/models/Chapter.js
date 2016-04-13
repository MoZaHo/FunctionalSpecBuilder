/**
 * Chapter.js
 *
 * @description :: TODO: You might write a short summary of how this model works and what it represents here.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */

module.exports = {

  attributes: {
    document_id : {
      type: 'int',
      required: true
    },
    title : {
      type: 'string',
      required: true
    },
    parent_id : {
      type: 'integer',
      required: true
    },
    order : {
      type: 'integer',
      defaultsTo : 0
    }
  }
};

