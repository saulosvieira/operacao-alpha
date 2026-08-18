import 'package:freezed_annotation/freezed_annotation.dart';

part 'ranking.freezed.dart';
part 'ranking.g.dart';

@freezed
abstract class RankingEntry with _$RankingEntry {
  const factory RankingEntry({
    @JsonKey(fromJson: _intFromJson) required int position,
    @JsonKey(name: 'userId', fromJson: _intFromJson) required int userId,
    @JsonKey(name: 'userName') required String userName,
    @JsonKey(fromJson: _intFromJson) required int score,
    @JsonKey(name: 'isCurrentUser') @Default(false) bool isCurrentUser,
  }) = _RankingEntry;

  factory RankingEntry.fromJson(Map<String, dynamic> json) =>
      _$RankingEntryFromJson(json);
}

@freezed
abstract class MyRankingPosition with _$MyRankingPosition {
  const factory MyRankingPosition({
    @JsonKey(fromJson: _nullableIntFromJson) int? position,
    @JsonKey(fromJson: _intFromJson) required int score,
  }) = _MyRankingPosition;

  factory MyRankingPosition.fromJson(Map<String, dynamic> json) =>
      _$MyRankingPositionFromJson(json);
}

int _intFromJson(dynamic value) {
  if (value is int) return value;
  if (value is String) return int.tryParse(value) ?? 0;
  if (value is double) return value.toInt();
  return 0;
}

int? _nullableIntFromJson(dynamic value) {
  if (value == null) return null;
  return _intFromJson(value);
}
