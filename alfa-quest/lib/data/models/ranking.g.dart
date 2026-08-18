// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'ranking.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

_RankingEntry _$RankingEntryFromJson(Map<String, dynamic> json) =>
    _RankingEntry(
      position: _intFromJson(json['position']),
      userId: _intFromJson(json['userId']),
      userName: json['userName'] as String,
      score: _intFromJson(json['score']),
      isCurrentUser: json['isCurrentUser'] as bool? ?? false,
    );

Map<String, dynamic> _$RankingEntryToJson(_RankingEntry instance) =>
    <String, dynamic>{
      'position': instance.position,
      'userId': instance.userId,
      'userName': instance.userName,
      'score': instance.score,
      'isCurrentUser': instance.isCurrentUser,
    };

_MyRankingPosition _$MyRankingPositionFromJson(Map<String, dynamic> json) =>
    _MyRankingPosition(
      position: _nullableIntFromJson(json['position']),
      score: _intFromJson(json['score']),
    );

Map<String, dynamic> _$MyRankingPositionToJson(_MyRankingPosition instance) =>
    <String, dynamic>{
      'position': instance.position,
      'score': instance.score,
    };
