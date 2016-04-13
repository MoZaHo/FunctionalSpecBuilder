/**
 * Article_part.js
 *
 * @description :: TODO: You might write a short summary of how this model works and what it represents here.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */

module.exports = {

  attributes: {
    article_id : {
      type : 'int',
      model : 'Article',
      required : true
    },

    type : {
      type : 'string',
      required : true
    },

    data : {
      type : 'text',
      required : true
    }

  }
};

